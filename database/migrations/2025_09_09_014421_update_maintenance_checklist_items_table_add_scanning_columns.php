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
        Schema::table('maintenance_checklist_items', function (Blueprint $table) {
            $table->boolean('is_scanned')->default(false);
            $table->timestamp('scanned_at')->nullable();
            $table->string('scanned_by')->nullable(); // GSU user who scanned
            $table->boolean('is_missing')->default(false);
            $table->text('missing_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_checklist_items', function (Blueprint $table) {
            $table->dropColumn([
                'is_scanned',
                'scanned_at',
                'scanned_by',
                'is_missing',
                'missing_reason'
            ]);
        });
    }
};
