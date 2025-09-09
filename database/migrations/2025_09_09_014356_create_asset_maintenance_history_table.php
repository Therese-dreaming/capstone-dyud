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
        Schema::create('asset_maintenance_history', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code');
            $table->unsignedBigInteger('maintenance_checklist_id');
            $table->string('start_status'); // OK, FOR REPAIR, FOR REPLACEMENT
            $table->string('end_status'); // OK, FOR REPAIR, FOR REPLACEMENT
            $table->string('scanned_by'); // GSU user who scanned
            $table->timestamp('scanned_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('asset_code')->references('asset_code')->on('assets')->onDelete('cascade');
            $table->foreign('maintenance_checklist_id')->references('id')->on('maintenance_checklists')->onDelete('cascade');
            
            $table->index(['asset_code', 'maintenance_checklist_id'], 'asset_maint_history_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_maintenance_history');
    }
};
