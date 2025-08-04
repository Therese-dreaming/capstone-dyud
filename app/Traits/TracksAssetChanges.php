<?php

namespace App\Traits;

use App\Models\AssetChange;
use Illuminate\Support\Facades\Auth;

trait TracksAssetChanges
{
    protected static function bootTracksAssetChanges()
    {
        static::updating(function ($asset) {
            $changes = [];
            $original = $asset->getOriginal();
            
            // Track changes for specific fields
            $fieldsToTrack = [
                'name' => AssetChange::TYPE_UPDATE,
                'location_id' => AssetChange::TYPE_LOCATION_CHANGE,
                'original_location_id' => AssetChange::TYPE_LOCATION_CHANGE,
                'purchase_cost' => AssetChange::TYPE_PRICE_CHANGE,
                'purchase_date' => AssetChange::TYPE_UPDATE,
                'condition' => AssetChange::TYPE_CONDITION_CHANGE,
                'status' => AssetChange::TYPE_STATUS_CHANGE,
                'category_id' => AssetChange::TYPE_CATEGORY_CHANGE,
                'description' => AssetChange::TYPE_UPDATE,
            ];
            
            foreach ($fieldsToTrack as $field => $changeType) {
                if (isset($original[$field]) && $asset->isDirty($field)) {
                    $oldValue = $original[$field];
                    $newValue = $asset->getAttribute($field);
                    
                    // Format values for better display
                    $oldValue = self::formatValueForDisplay($field, $oldValue);
                    $newValue = self::formatValueForDisplay($field, $newValue);
                    
                    $changes[] = [
                        'asset_id' => $asset->id,
                        'change_type' => $changeType,
                        'field' => $field,
                        'previous_value' => $oldValue,
                        'new_value' => $newValue,
                        'changed_by' => Auth::user() ? Auth::user()->name : 'System',
                        'user_id' => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            
            // Store changes after the asset is updated
            static::created(function ($asset) use ($changes) {
                foreach ($changes as $change) {
                    AssetChange::create($change);
                }
            });
            
            static::updated(function ($asset) use ($changes) {
                foreach ($changes as $change) {
                    AssetChange::create($change);
                }
            });
        });
    }
    
    protected static function formatValueForDisplay($field, $value)
    {
        if ($value === null) {
            return 'None';
        }
        
        return match($field) {
            'location_id', 'original_location_id' => self::formatLocationValue($value),
            'category_id' => self::formatCategoryValue($value),
            'purchase_cost' => '₱' . number_format($value, 2),
            'purchase_date' => $value ? date('M d, Y', strtotime($value)) : 'None',
            'condition' => ucfirst($value),
            'status' => ucfirst($value),
            default => $value
        };
    }
    
    protected static function formatLocationValue($locationId)
    {
        if (!$locationId) return 'None';
        
        $location = \App\Models\Location::find($locationId);
        return $location ? "{$location->building} - Floor {$location->floor} - Room {$location->room}" : "Location ID: {$locationId}";
    }
    
    protected static function formatCategoryValue($categoryId)
    {
        if (!$categoryId) return 'None';
        
        $category = \App\Models\Category::find($categoryId);
        return $category ? $category->name : "Category ID: {$categoryId}";
    }
    
    // Method to manually record changes (for borrowing approvals, returns, etc.)
    public static function recordChange($assetId, $changeType, $field, $previousValue = null, $newValue = null, $notes = null)
    {
        return AssetChange::create([
            'asset_id' => $assetId,
            'change_type' => $changeType,
            'field' => $field,
            'previous_value' => $previousValue,
            'new_value' => $newValue,
            'changed_by' => Auth::user() ? Auth::user()->name : 'System',
            'user_id' => Auth::id(),
            'notes' => $notes,
        ]);
    }
} 