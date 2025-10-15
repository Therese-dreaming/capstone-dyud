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
        Schema::table('assets', function (Blueprint $table) {
            // Depreciation method: straight_line, declining_balance, sum_of_years_digits
            $table->string('depreciation_method')->default('straight_line')->after('purchase_date');
            
            // Useful life in years
            $table->decimal('useful_life_years', 5, 2)->default(5)->after('depreciation_method');
            
            // Salvage value (residual value at end of useful life)
            $table->decimal('salvage_value', 12, 2)->default(0)->after('useful_life_years');
            
            // Declining balance rate (for declining balance method, typically 2 for double declining)
            $table->decimal('declining_balance_rate', 5, 2)->default(2)->after('salvage_value');
            
            // Date when depreciation starts (defaults to purchase_date)
            $table->date('depreciation_start_date')->nullable()->after('declining_balance_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'depreciation_method',
                'useful_life_years',
                'salvage_value',
                'declining_balance_rate',
                'depreciation_start_date'
            ]);
        });
    }
};
