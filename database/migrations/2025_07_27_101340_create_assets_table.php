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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('asset_code')->unique(); // for QR code
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('room_id');
            $table->text('description')->nullable();
            $table->decimal('purchase_cost', 12, 2);
            $table->date('purchase_date');
            $table->string('condition');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
