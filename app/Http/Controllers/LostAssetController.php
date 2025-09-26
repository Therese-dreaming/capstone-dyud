<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetChange;
use App\Models\LostAsset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LostAssetController extends Controller
{
    /**
     * Display a listing of lost assets.
     */
    public function index(Request $request)
    {
        $query = LostAsset::with(['asset.category', 'asset.location', 'reportedBy']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('asset', function($assetQuery) use ($search) {
                    $assetQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('asset_code', 'like', "%{$search}%");
                })
                ->orWhereHas('reportedBy', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                });
            });
        }
        
        $lostAssets = $query->latest()->paginate(15);
        
        // Overall counts from database (not page-limited)
        $counts = [
            'investigating' => LostAsset::where('status', LostAsset::STATUS_INVESTIGATING)->count(),
            'found' => LostAsset::where('status', 'found')->count(),
            'permanently_lost' => LostAsset::where('status', 'permanently_lost')->count(),
        ];
        
        // Check if user is GSU and return appropriate view
        if (auth()->user()->role === 'gsu') {
            return view('lost-assets.gsu-index', compact('lostAssets', 'counts'));
        }
        
        return view('lost-assets.index', compact('lostAssets', 'counts'));
    }

    /**
     * Show the form for reporting an asset as lost.
     */
    public function create(Asset $asset)
    {
        // Borrowings removed; no last borrower information available
        $lastBorrowing = null;

        // Check if user is GSU and return appropriate view
        if (auth()->user()->role === 'gsu') {
            return view('lost-assets.gsu-create', compact('asset', 'lastBorrowing'));
        }
        
        return view('lost-assets.create', compact('asset', 'lastBorrowing'));
    }

    /**
     * Store a newly created lost asset report.
     */
    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'last_seen_date' => 'required|date|before_or_equal:today',
            'description' => 'required|string|max:1000',
            'last_known_location' => 'nullable|string|max:500',
            'investigation_notes' => 'nullable|string|max:1000',
        ]);

        try {
            return DB::transaction(function () use ($validated, $asset, $request) {
                // Borrowings removed; no last borrower information available
                $lastBorrowing = null;

                // Determine last known location automatically from current location, fallback to original
                $asset->load(['location', 'originalLocation']);
                $lastKnownLocation = 'Unknown';
                if ($asset->location) {
                    $lastKnownLocation = $asset->location->building . ' - Floor ' . $asset->location->floor . ' - Room ' . $asset->location->room;
                } elseif ($asset->originalLocation) {
                    $lastKnownLocation = $asset->originalLocation->building . ' - Floor ' . $asset->originalLocation->floor . ' - Room ' . $asset->originalLocation->room;
                }

                $lostAsset = LostAsset::create([
                    'asset_id' => $asset->id,
                    'reported_by' => auth()->id(),
                    'last_borrower_id' => null,
                    'last_seen_date' => $validated['last_seen_date'],
                    'reported_date' => now()->toDateString(),
                    'description' => $validated['description'],
                    'last_known_location' => $lastKnownLocation,
                    'investigation_notes' => $validated['investigation_notes'],
                    'status' => LostAsset::STATUS_INVESTIGATING,
                ]);

                // Update asset status to 'Lost'
                $asset->update(['status' => 'Lost']);

                // Record the change
                \App\Traits\TracksAssetChanges::recordChange(
                    $asset->id,
                    AssetChange::TYPE_STATUS_CHANGE,
                    'status',
                    ucfirst($asset->getOriginal('status')),
                    'Lost',
                    "Asset reported as lost. Last seen: {$validated['last_seen_date']}. Description: {$validated['description']}"
                );

                return redirect()->route('lost-assets.index')
                    ->with('success', 'Asset reported as lost successfully. Investigation has been initiated.');
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to report asset as lost: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified lost asset.
     */
    public function show(LostAsset $lostAsset)
    {
        $lostAsset->load(['asset.category', 'asset.location', 'reportedBy']);
        
        // Check if user is GSU and return appropriate view
        if (auth()->user()->role === 'gsu') {
            return view('lost-assets.gsu-show', compact('lostAsset'));
        }
        
        return view('lost-assets.show', compact('lostAsset'));
    }

    /**
     * Update the status of a lost asset (found, permanently lost, etc.).
     */
    public function updateStatus(Request $request, LostAsset $lostAsset)
    {
        $validated = $request->validate([
            'status' => 'required|in:found,permanently_lost',
            'found_date' => 'required_if:status,found|date|before_or_equal:today',
            'found_location' => 'required_if:status,found|string|max:500',
            'found_notes' => 'nullable|string|max:1000',
        ]);

        try {
            return DB::transaction(function () use ($validated, $lostAsset) {
                $oldStatus = $lostAsset->status;
                
                $lostAsset->update([
                    'status' => $validated['status'],
                    'found_date' => $validated['status'] === 'found' ? $validated['found_date'] : null,
                    'found_location' => $validated['status'] === 'found' ? $validated['found_location'] : null,
                    'found_notes' => $validated['status'] === 'found' ? $validated['found_notes'] : null,
                ]);

                // Update asset status based on lost asset status
                if ($validated['status'] === 'found') {
                    $lostAsset->asset->update(['status' => 'Available']);
                    
                    \App\Traits\TracksAssetChanges::recordChange(
                        $lostAsset->asset->id,
                        AssetChange::TYPE_STATUS_CHANGE,
                        'status',
                        'Lost',
                        'Available',
                        "Asset found. Found date: {$validated['found_date']}. Location: {$validated['found_location']}"
                    );
                } elseif ($validated['status'] === 'permanently_lost') {
                    $lostAsset->asset->update(['status' => 'Lost']);
                    
                    \App\Traits\TracksAssetChanges::recordChange(
                        $lostAsset->asset->id,
                        AssetChange::TYPE_STATUS_CHANGE,
                        'status',
                        'Lost',
                        'Lost',
                        "Asset permanently lost. Investigation closed."
                    );
                }

                $statusMessage = $validated['status'] === 'found' ? 'Asset marked as found successfully.' : 'Asset marked as permanently lost.';
                
                return redirect()->back()
                    ->with('success', $statusMessage);
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update lost asset status: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified lost asset record.
     */
    public function destroy(LostAsset $lostAsset)
    {
        try {
            return DB::transaction(function () use ($lostAsset) {
                // If the asset is still marked as lost, restore it to available
                if ($lostAsset->asset->status === 'Lost') {
                    $lostAsset->asset->update(['status' => 'Available']);
                    
                    \App\Traits\TracksAssetChanges::recordChange(
                        $lostAsset->asset->id,
                        AssetChange::TYPE_STATUS_CHANGE,
                        'status',
                        'Lost',
                        'Available',
                        "Lost asset record deleted. Asset restored to available status."
                    );
                }
                
                $lostAsset->delete();
                
                return redirect()->route('lost-assets.index')
                    ->with('success', 'Lost asset record deleted successfully.');
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete lost asset record: ' . $e->getMessage());
        }
    }

    public function export(Request $request): StreamedResponse
    {
        $query = LostAsset::with(['asset.category', 'asset.location', 'reportedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('asset', function($assetQuery) use ($search) {
                    $assetQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('asset_code', 'like', "%{$search}%");
                })
                ->orWhereHas('reportedBy', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $rows = $query->orderBy('reported_date', 'desc')->get();

        $filename = 'lost-assets-' . now()->format('Ymd_His') . '.xls';

        return response()->streamDownload(function() use ($rows) {
            echo '<?xml version="1.0"?>';
            echo '<?mso-application progid="Excel.Sheet"?>';
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">';
            echo '<Styles>';
            echo '<Style ss:ID="Title"><Font ss:Bold="1" ss:Size="16" ss:Color="#800000"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>';
            echo '<Style ss:ID="Subtitle"><Font ss:Size="12" ss:Color="#666666"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>';
            echo '<Style ss:ID="Header"><Font ss:Bold="1" ss:Size="11" ss:Color="#FFFFFF"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/><Interior ss:Color="#800000" ss:Pattern="Solid"/><Borders><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CCCCCC"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CCCCCC"/><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CCCCCC"/><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CCCCCC"/></Borders></Style>';
            echo '<Style ss:ID="Cell"><Alignment ss:Vertical="Center"/><Borders><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/></Borders></Style>';
            echo '<Style ss:ID="CellAlt"><Alignment ss:Vertical="Center"/><Interior ss:Color="#F8F9FA" ss:Pattern="Solid"/><Borders><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/></Borders></Style>';
            echo '</Styles>';

            echo '<Worksheet ss:Name="Lost Assets">';
            echo '<Table>';
            echo '<Column ss:Width="40"/>';
            echo '<Column ss:Width="120"/>';
            echo '<Column ss:Width="240"/>';
            echo '<Column ss:Width="140"/>';
            echo '<Column ss:Width="140"/>';
            echo '<Column ss:Width="100"/>';
            echo '<Column ss:Width="360"/>';
            echo '<Column ss:Width="120"/>';

            echo '<Row ss:Height="28"><Cell ss:MergeAcross="7" ss:StyleID="Title"><Data ss:Type="String">LOST ASSETS REPORT</Data></Cell></Row>';
            echo '<Row ss:Height="20"><Cell ss:MergeAcross="7" ss:StyleID="Subtitle"><Data ss:Type="String">Generated on ' . e(now()->format('F d, Y \\a\\t g:i A')) . '</Data></Cell></Row>';
            echo '<Row ss:Height="6"/>';

            $headers = ['No.','Asset Code','Asset Name','Category','Reported By','Reported Date','Description','Status'];
            echo '<Row ss:Height="24">';
            foreach ($headers as $h) {
                echo '<Cell ss:StyleID="Header"><Data ss:Type="String">' . e($h) . '</Data></Cell>';
            }
            echo '</Row>';

            $i = 0;
            foreach ($rows as $row) {
                $i++;
                $alt = ($i % 2 === 0);
                $style = $alt ? 'CellAlt' : 'Cell';
                echo '<Row ss:Height="22">';
                echo '<Cell ss:StyleID="' . $style . '"><Data ss:Type="Number">' . (int)$i . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . e($row->asset->asset_code ?? '') . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . e($row->asset->name ?? '') . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . e(optional($row->asset->category)->name ?? '') . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . e(optional($row->reportedBy)->name ?? '') . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . e(optional($row->reported_date)->format('Y-m-d') ?? '') . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . e($row->description ?? '') . '</Data></Cell>';
                echo '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . e($row->getStatusLabel()) . '</Data></Cell>';
                echo '</Row>';
            }

            echo '</Table></Worksheet></Workbook>';
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }
}
