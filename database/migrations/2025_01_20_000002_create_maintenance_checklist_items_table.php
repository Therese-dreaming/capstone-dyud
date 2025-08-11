<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_checklist_id')->constrained()->onDelete('cascade');
            $table->string('particulars'); // Door/s, Doorknob/s, Tiles, etc.
            $table->integer('quantity'); // 2, 380, 0, etc.
            $table->string('start_status'); // OK
            $table->string('end_status'); // OK, FOR REPAIR, FOR REPLACEMENT
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_checklist_items');
    }
}; 