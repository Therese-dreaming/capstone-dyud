<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('maintenance_checklist_items', function (Blueprint $table) {
            $table->string('asset_code')->nullable()->after('maintenance_checklist_id');
        });
    }

    public function down()
    {
        Schema::table('maintenance_checklist_items', function (Blueprint $table) {
            $table->dropColumn('asset_code');
        });
    }
};
