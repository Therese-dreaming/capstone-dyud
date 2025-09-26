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
        Schema::table('assets', function (Blueprint $table) {
            // Add semester tracking columns
            $table->foreignId('registered_semester_id')->nullable()->after('created_by')->constrained('semesters')->onDelete('set null');
            $table->foreignId('disposed_semester_id')->nullable()->after('registered_semester_id')->constrained('semesters')->onDelete('set null');
            $table->foreignId('lost_semester_id')->nullable()->after('disposed_semester_id')->constrained('semesters')->onDelete('set null');
            
            // Add indexes for performance
            $table->index('registered_semester_id');
            $table->index('disposed_semester_id');
            $table->index('lost_semester_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['registered_semester_id']);
            $table->dropForeign(['disposed_semester_id']);
            $table->dropForeign(['lost_semester_id']);
            $table->dropColumn(['registered_semester_id', 'disposed_semester_id', 'lost_semester_id']);
        });
    }
};
