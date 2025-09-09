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
        Schema::table('maintenance_checklist_items', function (Blueprint $table) {
            // Make end_status nullable since it should only be set when GSU scans assets
            $table->string('end_status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_checklist_items', function (Blueprint $table) {
            // Revert back to not nullable
            $table->string('end_status')->nullable(false)->change();
        });
    }
};