<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('maintenance_checklists', function (Blueprint $table) {
            $table->date('start_of_sy_date')->nullable()->after('school_year');
            $table->date('end_of_sy_date')->nullable()->after('start_of_sy_date');
            $table->dateTime('started_at')->nullable()->after('acknowledged_at');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_checklists', function (Blueprint $table) {
            $table->dropColumn(['start_of_sy_date', 'end_of_sy_date', 'started_at']);
        });
    }
};


