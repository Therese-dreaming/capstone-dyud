<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispose extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'disposal_reason',
        'disposal_date',
        'disposed_by',
    ];

    protected $casts = [
        'disposal_date' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'disposed_by');
    }
}
