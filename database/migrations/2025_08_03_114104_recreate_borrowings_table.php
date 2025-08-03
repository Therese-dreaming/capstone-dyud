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
        // Drop the existing borrowings table
        Schema::dropIfExists('borrowings');
        
        // Create the new borrowings table
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            
            // User and Asset relationships
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            
            // Borrower Information (for display purposes)
            $table->string('borrower_name');
            $table->string('borrower_id_number');
            
            // Borrowing Details
            $table->text('purpose');
            $table->date('request_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            
            // Approval Information
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            
            // Status: pending, approved, rejected, returned, overdue
            $table->enum('status', ['pending', 'approved', 'rejected', 'returned', 'overdue'])->default('pending');
            
            // Timestamps
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index(['asset_id', 'status']);
            $table->index(['status', 'due_date']);
            $table->index('borrower_id_number');
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
