<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            
            // Borrower Information
            $table->string('borrower_name');
            $table->string('borrower_id_number');
            
            // Location and Category
            $table->string('room');
            $table->string('category');
            
            // Items being borrowed (stored as JSON)
            $table->json('items');
            
            // Purpose of borrowing
            $table->text('purpose')->nullable();
            
            // Date and Time Information
            $table->date('borrow_date');
            $table->time('borrow_time');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            
            // Status: active, returned, overdue, cancelled
            $table->enum('status', ['active', 'returned', 'overdue', 'cancelled'])->default('active');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['status', 'due_date']);
            $table->index('borrower_id_number');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
