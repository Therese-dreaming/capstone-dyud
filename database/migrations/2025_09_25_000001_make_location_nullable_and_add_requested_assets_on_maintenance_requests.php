<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            // Make location_id nullable to support specific-assets requests
            $table->unsignedBigInteger('location_id')->nullable()->change();
            // Store requested asset codes for specific-assets scope
            $table->text('requested_asset_codes')->nullable()->after('location_id');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropColumn('requested_asset_codes');
            // Revert to not nullable (may fail if data exists without location)
            $table->unsignedBigInteger('location_id')->nullable(false)->change();
        });
    }
};


