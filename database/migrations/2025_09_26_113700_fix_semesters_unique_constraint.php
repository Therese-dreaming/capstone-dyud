<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the problematic unique constraint
        Schema::table('semesters', function (Blueprint $table) {
            $table->dropIndex('unique_current_semester');
        });
        
        // We'll handle the "only one current semester" logic in the application
        // instead of using a database constraint since MySQL doesn't support
        // partial unique constraints properly
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the original constraint (this will cause the same issue)
        Schema::table('semesters', function (Blueprint $table) {
            $table->unique(['is_current'], 'unique_current_semester');
        });
    }
};
