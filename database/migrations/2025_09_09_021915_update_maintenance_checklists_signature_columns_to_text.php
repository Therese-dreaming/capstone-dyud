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
            // Change signature columns from string to text to accommodate base64 data URLs
            $table->text('instructor_signature')->nullable()->change();
            $table->text('checked_by_signature')->nullable()->change();
            $table->text('gsu_staff_signature')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_checklists', function (Blueprint $table) {
            // Revert back to string columns
            $table->string('instructor_signature')->nullable()->change();
            $table->string('checked_by_signature')->nullable()->change();
            $table->string('gsu_staff_signature')->nullable()->change();
        });
    }
};