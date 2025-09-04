<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'change_type',
        'field',
        'previous_value',
        'new_value',
        'changed_by',
        'user_id',
        'notes'
    ];

    // Change type constants
    const TYPE_UPDATE = 'update';
    const TYPE_LOCATION_CHANGE = 'location_change';
    const TYPE_STATUS_CHANGE = 'status_change';
    const TYPE_CONDITION_CHANGE = 'condition_change';
    const TYPE_PRICE_CHANGE = 'price_change';
    const TYPE_CATEGORY_CHANGE = 'category_change';

    const TYPE_DISPOSED = 'disposed';

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getChangeTypeLabel()
    {
        return match($this->change_type) {
            self::TYPE_UPDATE => 'General Update',
            self::TYPE_LOCATION_CHANGE => 'Location Change',
            self::TYPE_STATUS_CHANGE => 'Status Change',
            self::TYPE_CONDITION_CHANGE => 'Condition Change',
            self::TYPE_PRICE_CHANGE => 'Price Change',
            self::TYPE_CATEGORY_CHANGE => 'Category Change',

            self::TYPE_DISPOSED => 'Asset Disposed',
            default => ucfirst(str_replace('_', ' ', $this->change_type))
        };
    }

    public function getFieldLabel()
    {
        return match($this->field) {
            'name' => 'Asset Name',
            'location_id' => 'Location',
            'original_location_id' => 'Original Location',
            'purchase_cost' => 'Purchase Cost',
            'purchase_date' => 'Purchase Date',
            'condition' => 'Condition',
            'status' => 'Status',
            'category_id' => 'Category',
            'description' => 'Description',
            default => ucfirst(str_replace('_', ' ', $this->field))
        };
    }
}
