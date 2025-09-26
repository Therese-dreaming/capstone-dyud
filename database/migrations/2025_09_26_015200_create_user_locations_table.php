<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure a user can only be assigned to a location once
            $table->unique(['user_id', 'location_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_locations');
    }
};
