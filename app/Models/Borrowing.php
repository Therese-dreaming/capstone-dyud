<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrower_name',
        'borrower_id_number',
        'room',
        'category',
        'items',
        'purpose',
        'borrow_date',
        'borrow_time',
        'due_date',
        'return_date',
        'status'
    ];

    protected $casts = [
        'items' => 'array',
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
    ];

    public function isOverdue()
    {
        if ($this->status === 'returned') {
            return false;
        }
        
        return now()->greaterThan($this->due_date);
    }
}
