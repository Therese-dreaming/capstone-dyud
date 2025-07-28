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
        // Rename rooms table to locations
        Schema::rename('rooms', 'locations');
        
        // Modify the locations table structure
        Schema::table('locations', function (Blueprint $table) {
            // Remove the old 'name' column
            $table->dropColumn('name');
            // Keep 'building' and add 'floor' and 'room'
            $table->string('floor');
            $table->string('room');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the column changes
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['floor', 'room']);
            $table->string('name');
        });
        
        // Rename back to rooms
        Schema::rename('locations', 'rooms');
    }
};
