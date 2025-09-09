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
        // Add location tracking to maintenance_checklist_items
        Schema::table('maintenance_checklist_items', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('maintenance_checklist_id')
                  ->constrained('locations')->onDelete('set null');
            $table->string('location_name')->nullable()->after('location_id'); // Cached location name for historical reference
        });

        // Add location tracking to asset_maintenance_history
        Schema::table('asset_maintenance_history', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('maintenance_checklist_id')
                  ->constrained('locations')->onDelete('set null');
            $table->string('location_name')->nullable()->after('location_id'); // Cached location name for historical reference
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_checklist_items', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn(['location_id', 'location_name']);
        });

        Schema::table('asset_maintenance_history', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn(['location_id', 'location_name']);
        });
    }
};