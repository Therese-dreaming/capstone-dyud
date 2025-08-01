<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Warranty;
use App\Models\Dispose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with(['category', 'location', 'warranty'])->paginate(10);
        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        $categories = Category::all();
        $locations = Location::all();
        return view('assets.create', compact('categories', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'condition' => 'required|in:Good,Fair,Poor',
            'description' => 'nullable|string',
            'purchase_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'status' => 'required|in:Available,Disposed',
            // Warranty fields
            'manufacturer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'warranty_expiry' => 'required|date'
        ]);

        try {
            return DB::transaction(function () use ($validated, $request) {
                // Generate asset code
                $category = Category::find($request->category_id);
                $latestAsset = Asset::where('category_id', $category->id)->latest()->first();
                $sequence = $latestAsset ? (int)substr($latestAsset->asset_code, -4) + 1 : 1;
                $validated['asset_code'] = $category->code . str_pad($sequence, 4, '0', STR_PAD_LEFT);

                // Create asset
                $asset = Asset::create([
                    'asset_code' => $validated['asset_code'],
                    'name' => $validated['name'],
                    'category_id' => $validated['category_id'],
                    'location_id' => $validated['location_id'],
                    'condition' => $validated['condition'],
                    'description' => $validated['description'],
                    'purchase_cost' => $validated['purchase_cost'],
                    'purchase_date' => $validated['purchase_date'],
                    'status' => $validated['status']
                ]);

                // Create warranty
                $warranty = Warranty::create([
                    'asset_id' => $asset->id,
                    'manufacturer' => $validated['manufacturer'],
                    'model' => $validated['model'],
                    'warranty_expiry' => $validated['warranty_expiry']
                ]);

                return redirect()->route('assets.index')
                    ->with('success', 'Asset and warranty created successfully.');
            });
        } catch (\Exception $e) {
            \Log::error('Asset and warranty creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create asset and warranty: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Asset $asset)
    {
        $asset->load(['category', 'location', 'warranty', 'disposes']);
        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $asset->load('warranty');
        $categories = Category::all();
        $locations = Location::all();
        return view('assets.edit', compact('asset', 'categories', 'locations'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'condition' => 'required|in:Good,Fair,Poor',
            'description' => 'nullable|string',
            'purchase_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'status' => 'required|in:Available,Disposed',
            // Warranty fields
            'manufacturer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'warranty_expiry' => 'required|date'
        ]);

        // Update asset
        $asset->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'location_id' => $validated['location_id'],
            'condition' => $validated['condition'],
            'description' => $validated['description'],
            'purchase_cost' => $validated['purchase_cost'],
            'purchase_date' => $validated['purchase_date'],
            'status' => $validated['status']
        ]);

        // Update or create warranty
        $asset->warranty()->updateOrCreate(
            ['asset_id' => $asset->id],
            [
                'manufacturer' => $validated['manufacturer'],
                'model' => $validated['model'],
                'warranty_expiry' => $validated['warranty_expiry']
            ]
        );

        return redirect()->route('assets.index')
            ->with('success', 'Asset updated successfully.');
    }

    public function dispose(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'disposal_reason' => 'required|string|max:1000',
        ]);

        // Check if asset is not already disposed
        if ($asset->status === 'Disposed') {
            return redirect()->route('assets.index')
                ->with('error', 'Asset is already disposed.');
        }

        try {
            return DB::transaction(function () use ($asset, $validated) {
                // Create disposal record
                Dispose::create([
                    'asset_id' => $asset->id,
                    'disposal_reason' => $validated['disposal_reason'],
                    'disposal_date' => now()->toDateString(),
                    'disposed_by' => 'System Admin', // You might want to use auth()->user()->name if you have authentication
                ]);

                // Update asset status to disposed
                $asset->update([
                    'status' => 'Disposed'
                ]);

                return redirect()->route('assets.index')
                    ->with('success', "Asset {$asset->asset_code} has been disposed successfully.");
            });
        } catch (\Exception $e) {
            Log::error('Asset disposal failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to dispose asset: ' . $e->getMessage());
        }
    }

    public function destroy(Asset $asset)
    {
        $assetCode = $asset->asset_code;
        $asset->delete();
        
        return redirect()->route('assets.index')
            ->with('success', "Asset {$assetCode} has been deleted successfully.");
    }

    public function report(Request $request)
    {
        $query = Asset::with(['category', 'location', 'warranty']);

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('purchase_date_from')) {
            $query->whereDate('purchase_date', '>=', $request->purchase_date_from);
        }

        if ($request->filled('purchase_date_to')) {
            $query->whereDate('purchase_date', '<=', $request->purchase_date_to);
        }

        if ($request->filled('cost_min')) {
            $query->where('purchase_cost', '>=', $request->cost_min);
        }

        if ($request->filled('cost_max')) {
            $query->where('purchase_cost', '<=', $request->cost_max);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('asset_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Get filtered assets
        $assets = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get summary statistics
        $totalAssets = Asset::count();
        $totalValue = Asset::sum('purchase_cost');
        $availableAssets = Asset::where('status', 'Available')->count();
        $disposedAssets = Asset::where('status', 'Disposed')->count();
        $inUseAssets = Asset::where('status', 'In Use')->count();
        $lostAssets = Asset::where('status', 'Lost')->count();
        
        // Assets by condition
        $goodCondition = Asset::where('condition', 'Good')->count();
        $fairCondition = Asset::where('condition', 'Fair')->count();
        $poorCondition = Asset::where('condition', 'Poor')->count();
        
        // Assets by category
        $assetsByCategory = Asset::select('categories.name', DB::raw('count(*) as total'))
            ->join('categories', 'assets.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->get();
            
        // Get filter options
        $categories = Category::all();
        $locations = Location::all();
        
        return view('assets.report', compact(
            'assets', 'categories', 'locations', 'totalAssets', 'totalValue',
            'availableAssets', 'disposedAssets', 'inUseAssets', 'lostAssets',
            'goodCondition', 'fairCondition', 'poorCondition', 'assetsByCategory'
        ));
    }
}
