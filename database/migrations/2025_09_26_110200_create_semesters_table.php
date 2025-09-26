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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "1st Semester", "2nd Semester", "Summer"
            $table->string('academic_year'); // e.g., "2025-2026"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false); // Only one semester can be current at a time
            $table->boolean('is_active')->default(true); // For enabling/disabling semesters
            $table->text('description')->nullable(); // Optional description
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['start_date', 'end_date']);
            $table->index('is_current');
            $table->index('academic_year');
            
            // Ensure only one current semester at a time
            $table->unique(['is_current'], 'unique_current_semester')->where('is_current', true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
