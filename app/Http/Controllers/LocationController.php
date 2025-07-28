<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('building')->orderBy('floor')->orderBy('room')->get();
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building' => 'required|string|max:255',
            'floor' => 'required|string|max:255',
            'room' => 'required|string|max:255',
        ]);
        
        try {
            Location::create($validated);
            return redirect()->route('locations.index')->with('success', 'Location added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to add location.');
        }
    }

    public function show(Location $location)
    {
        $assets = $location->assets()->with('category')->paginate(10);
        return view('locations.show', compact('location', 'assets'));
    }

    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'building' => 'required|string|max:255',
            'floor' => 'required|string|max:255',
            'room' => 'required|string|max:255',
        ]);
        
        try {
            $location->update($validated);
            return redirect()->route('locations.index')->with('success', 'Location updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to update location.');
        }
    }

    public function destroy(Location $location)
    {
        try {
            // Check if location has assets
            if ($location->assets()->count() > 0) {
                return redirect()->route('locations.index')
                    ->with('error', 'Cannot delete location. It has assets assigned to it.');
            }
            
            $location->delete();
            return redirect()->route('locations.index')
                ->with('success', 'Location deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('locations.index')
                ->with('error', 'Failed to delete location.');
        }
    }
}
