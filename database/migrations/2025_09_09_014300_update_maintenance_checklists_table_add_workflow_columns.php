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
        Schema::table('maintenance_checklists', function (Blueprint $table) {
            $table->enum('status', ['created', 'acknowledged', 'in_progress', 'completed'])->default('created');
            $table->timestamp('acknowledged_at')->nullable();
            $table->unsignedBigInteger('acknowledged_by')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->boolean('has_missing_assets')->default(false);
            $table->text('missing_assets_acknowledged')->nullable(); // JSON of acknowledged missing assets
            
            $table->foreign('acknowledged_by')->references('id')->on('users');
            $table->foreign('completed_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_checklists', function (Blueprint $table) {
            $table->dropForeign(['acknowledged_by']);
            $table->dropForeign(['completed_by']);
            $table->dropColumn([
                'status',
                'acknowledged_at',
                'acknowledged_by',
                'completed_at',
                'completed_by',
                'has_missing_assets',
                'missing_assets_acknowledged'
            ]);
        });
    }
};
