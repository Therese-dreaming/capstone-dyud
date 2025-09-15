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
        // Add status column to assets table for approval workflow
        Schema::table('assets', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])
                  ->default('approved')
                  ->after('status');
            $table->text('rejection_reason')->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('rejection_reason');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            $table->unsignedBigInteger('created_by')->nullable()->after('approved_by');
            
            // Add foreign key constraints
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Make location_id nullable for purchasing workflow
        Schema::table('assets', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'approval_status',
                'rejection_reason', 
                'approved_at',
                'approved_by',
                'created_by'
            ]);
        });

        // Note: We don't revert location_id to non-nullable as it might break existing data
    }
};
