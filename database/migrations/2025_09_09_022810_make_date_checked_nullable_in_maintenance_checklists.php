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
        Schema::table('maintenance_checklists', function (Blueprint $table) {
            // Make date_checked nullable since it should only be set when GSU completes maintenance
            $table->date('date_checked')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_checklists', function (Blueprint $table) {
            // Revert back to not nullable
            $table->date('date_checked')->nullable(false)->change();
        });
    }
};