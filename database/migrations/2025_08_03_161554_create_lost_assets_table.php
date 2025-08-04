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
        Schema::create('lost_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('last_borrower_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('last_seen_date')->nullable();
            $table->date('reported_date');
            $table->text('description');
            $table->text('last_known_location')->nullable();
            $table->text('investigation_notes')->nullable();
            $table->enum('status', ['investigating', 'found', 'permanently_lost'])->default('investigating');
            $table->date('found_date')->nullable();
            $table->text('found_location')->nullable();
            $table->text('found_notes')->nullable();
            $table->timestamps();
            
            $table->index(['asset_id', 'status']);
            $table->index(['reported_by', 'status']);
            $table->index('reported_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_assets');
    }
};
