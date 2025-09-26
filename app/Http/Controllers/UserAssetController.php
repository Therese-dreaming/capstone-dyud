<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Asset;

class UserAssetController extends Controller
{
    /**
     * Display assets owned by the authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        
        // Only regular users can have owned assets
        if ($user->role !== 'user') {
            abort(403, 'Only regular users can view owned assets.');
        }

        // Get assets in locations owned by this user
        $assets = $user->ownedAssets()->paginate(15);
        $ownedLocations = $user->ownedLocations;

        return view('user-assets.index', compact('assets', 'ownedLocations'));
    }

    /**
     * Show details of a specific asset owned by the user
     */
    public function show(Asset $asset)
    {
        $user = Auth::user();
        
        // Check if user owns the location where this asset is located
        if (!$user->ownsLocation($asset->location_id)) {
            abort(403, 'You do not have permission to view this asset.');
        }

        return view('user-assets.show', compact('asset'));
    }

    /**
     * API: Get asset by code (only if user owns the location)
     */
    public function getAssetByCode($code)
    {
        $user = Auth::user();
        
        // Only regular users can access this endpoint
        if ($user->role !== 'user') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Find the asset by code
        $asset = Asset::where('asset_code', $code)->first();
        
        if (!$asset) {
            return response()->json(['error' => 'Asset not found'], 404);
        }

        // Check if user owns the location where this asset is located
        if (!$user->ownsLocation($asset->location_id)) {
            return response()->json(['error' => 'You do not have permission to access this asset'], 403);
        }

        return response()->json([
            'asset_code' => $asset->asset_code,
            'name' => $asset->name,
            'location' => [
                'id' => $asset->location->id,
                'building' => $asset->location->building,
                'floor' => $asset->location->floor,
                'room' => $asset->location->room,
                'full_name' => $asset->location->building . ' - Floor ' . $asset->location->floor . ' - Room ' . $asset->location->room
            ]
        ]);
    }
}
