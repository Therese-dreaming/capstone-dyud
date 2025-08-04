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
        Schema::create('asset_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('change_type'); // 'update', 'location_change', 'status_change', etc.
            $table->string('field'); // 'name', 'location_id', 'purchase_cost', 'condition', etc.
            $table->text('previous_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('changed_by'); // Name of the user who made the change
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User ID if available
            $table->text('notes')->nullable(); // Additional notes about the change
            $table->timestamps();
            
            $table->index(['asset_id', 'created_at']);
            $table->index('change_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_changes');
    }
};
