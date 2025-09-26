<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('maintenance_checklists', function (Blueprint $table) {
            $table->string('room_number')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('maintenance_checklists', function (Blueprint $table) {
            $table->string('room_number')->nullable(false)->change();
        });
    }
};
