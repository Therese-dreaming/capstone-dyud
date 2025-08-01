<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{
    /**
     * Display maintenance history for all assets with filtering.
     */
    public function history(Request $request)
    {
        $query = Maintenance::with(['asset'])->orderBy('scheduled_date', 'desc');

        // Apply filters
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('scheduled_from')) {
            $query->whereDate('scheduled_date', '>=', $request->scheduled_from);
        }

        if ($request->filled('scheduled_to')) {
            $query->whereDate('scheduled_date', '<=', $request->scheduled_to);
        }

        if ($request->filled('completed_from')) {
            $query->whereDate('completed_date', '>=', $request->completed_from);
        }

        if ($request->filled('completed_to')) {
            $query->whereDate('completed_date', '<=', $request->completed_to);
        }

        $maintenances = $query->paginate(15);

        return view('maintenances.history', compact('maintenances'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Asset $asset)
    {
        $maintenances = $asset->maintenances()->orderBy('scheduled_date', 'desc')->paginate(10);
        return view('maintenances.index', compact('asset', 'maintenances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Asset $asset)
    {
        return view('maintenances.create', compact('asset'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'type' => 'required|in:Preventive,Corrective,Emergency',
            'technician' => 'required|string|max:255',
            'status' => 'required|in:Scheduled,In Progress,Completed,Cancelled',
            'scheduled_date' => 'required|date',
            'completed_date' => 'nullable|date|after_or_equal:scheduled_date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0'
        ]);

        try {
            $validated['asset_id'] = $asset->id;
            Maintenance::create($validated);

            return redirect()->route('maintenances.index', $asset)
                ->with('success', 'Maintenance record created successfully.');
        } catch (\Exception $e) {
            Log::error('Maintenance creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create maintenance record: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset, Maintenance $maintenance)
    {
        return view('maintenances.show', compact('asset', 'maintenance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset, Maintenance $maintenance)
    {
        return view('maintenances.edit', compact('asset', 'maintenance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'type' => 'required|in:Preventive,Corrective,Emergency',
            'technician' => 'required|string|max:255',
            'status' => 'required|in:Scheduled,In Progress,Completed,Cancelled',
            'scheduled_date' => 'required|date',
            'completed_date' => 'nullable|date|after_or_equal:scheduled_date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0'
        ]);

        try {
            $maintenance->update($validated);

            return redirect()->route('maintenances.index', $asset)
                ->with('success', 'Maintenance record updated successfully.');
        } catch (\Exception $e) {
            Log::error('Maintenance update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update maintenance record: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset, Maintenance $maintenance)
    {
        try {
            $maintenance->delete();
            
            return redirect()->route('maintenances.index', $asset)
                ->with('success', 'Maintenance record deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Maintenance deletion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete maintenance record.');
        }
    }
}
