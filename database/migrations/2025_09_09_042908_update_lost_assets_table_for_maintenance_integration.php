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
        Schema::table('lost_assets', function (Blueprint $table) {
            // Remove columns that are no longer needed
            $table->dropForeign(['last_borrower_id']);
            $table->dropColumn(['last_borrower_id', 'last_seen_date', 'description', 'found_location']);
            
            // Update status enum to match new requirements
            $table->dropColumn('status');
            $table->enum('status', ['lost', 'resolved'])->default('lost')->after('investigation_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lost_assets', function (Blueprint $table) {
            // Add back the removed columns
            $table->foreignId('last_borrower_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('last_seen_date')->nullable();
            $table->text('description');
            $table->text('found_location')->nullable();
            
            // Revert status enum
            $table->dropColumn('status');
            $table->enum('status', ['investigating', 'found', 'permanently_lost'])->default('investigating');
        });
    }
};
