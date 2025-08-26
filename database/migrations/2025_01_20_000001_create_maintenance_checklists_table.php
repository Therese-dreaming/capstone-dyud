<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('school_year'); // 2022-2023
            $table->string('department'); // Grade School
            $table->date('date_reported');
            $table->string('program')->nullable(); // N/A
            $table->string('room_number'); // Room 9
            $table->string('instructor'); // Ms. Darlyn
            $table->string('instructor_signature')->nullable();
            $table->string('checked_by'); // Ms. Shriday
            $table->string('checked_by_signature')->nullable();
            $table->date('date_checked');
            $table->string('gsu_staff'); // Mr. Joseph
            $table->string('gsu_staff_signature')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_checklists');
    }
}; 