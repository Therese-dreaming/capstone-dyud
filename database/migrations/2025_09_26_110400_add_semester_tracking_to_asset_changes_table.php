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
        Schema::table('asset_changes', function (Blueprint $table) {
            // Add semester tracking to asset changes
            $table->foreignId('semester_id')->nullable()->after('user_id')->constrained('semesters')->onDelete('set null');
            
            // Add index for performance
            $table->index('semester_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_changes', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->dropColumn('semester_id');
        });
    }
};
