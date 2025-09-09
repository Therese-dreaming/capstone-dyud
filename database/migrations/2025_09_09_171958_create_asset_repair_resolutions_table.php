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
        Schema::create('asset_repair_resolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('resolved_by')->constrained('users')->onDelete('cascade');
            $table->enum('previous_status', ['For Repair', 'For Maintenance', 'For Replacement']);
            $table->enum('resolution_status', ['Repaired', 'Disposed', 'Replaced', 'Returned to Service']);
            $table->text('resolution_notes')->nullable();
            $table->text('actions_taken')->nullable();
            $table->decimal('repair_cost', 10, 2)->nullable();
            $table->date('resolution_date');
            $table->string('vendor_name')->nullable();
            $table->string('invoice_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_repair_resolutions');
    }
};