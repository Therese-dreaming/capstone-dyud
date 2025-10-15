<?php

namespace App\Services;

use App\Models\Asset;
use Carbon\Carbon;

class DepreciationService
{
    /**
     * Calculate depreciation for an asset
     */
    public function calculateDepreciation(Asset $asset, ?Carbon $asOfDate = null): array
    {
        $asOfDate = $asOfDate ?? now();
        
        // Get depreciation start date
        $startDate = $asset->depreciation_start_date 
            ? Carbon::parse($asset->depreciation_start_date)
            : Carbon::parse($asset->purchase_date);
        
        // If asset hasn't started depreciating yet
        if ($asOfDate->lt($startDate)) {
            return [
                'method' => $asset->depreciation_method,
                'purchase_cost' => (float) $asset->purchase_cost,
                'salvage_value' => (float) $asset->salvage_value,
                'useful_life_years' => (float) $asset->useful_life_years,
                'age_years' => 0,
                'age_months' => 0,
                'accumulated_depreciation' => 0,
                'current_book_value' => (float) $asset->purchase_cost,
                'annual_depreciation' => 0,
                'monthly_depreciation' => 0,
                'depreciation_rate' => 0,
                'is_fully_depreciated' => false,
                'remaining_useful_life_years' => (float) $asset->useful_life_years,
            ];
        }
        
        // Calculate age in years and months
        $ageInMonths = $startDate->diffInMonths($asOfDate);
        $ageInYears = $ageInMonths / 12;
        
        // Calculate age breakdown (years, months, days)
        $ageDiff = $startDate->diff($asOfDate);
        $ageYearsInt = $ageDiff->y;
        $ageMonthsInt = $ageDiff->m;
        $ageDaysInt = $ageDiff->d;
        
        // Calculate depreciation based on method
        $result = match($asset->depreciation_method) {
            'declining_balance' => $this->calculateDecliningBalance($asset, $ageInYears),
            'sum_of_years_digits' => $this->calculateSumOfYearsDigits($asset, $ageInYears),
            default => $this->calculateStraightLine($asset, $ageInYears),
        };
        
        // Add common fields
        $result['method'] = $asset->depreciation_method;
        $result['purchase_cost'] = (float) $asset->purchase_cost;
        $result['salvage_value'] = (float) $asset->salvage_value;
        $result['useful_life_years'] = (float) $asset->useful_life_years;
        $result['age_years'] = $ageYearsInt;
        $result['age_months'] = $ageMonthsInt;
        $result['age_days'] = $ageDaysInt;
        $result['age_total_months'] = $ageInMonths; // Keep for calculations
        $result['remaining_useful_life_years'] = max(0, (float) $asset->useful_life_years - $ageInYears);
        
        return $result;
    }
    
    /**
     * Straight-line depreciation
     * Formula: (Cost - Salvage Value) / Useful Life
     */
    private function calculateStraightLine(Asset $asset, float $ageInYears): array
    {
        $depreciableAmount = $asset->purchase_cost - $asset->salvage_value;
        $annualDepreciation = $depreciableAmount / $asset->useful_life_years;
        $monthlyDepreciation = $annualDepreciation / 12;
        
        // Calculate accumulated depreciation
        $accumulatedDepreciation = min(
            $annualDepreciation * $ageInYears,
            $depreciableAmount
        );
        
        $currentBookValue = max(
            $asset->purchase_cost - $accumulatedDepreciation,
            $asset->salvage_value
        );
        
        $isFullyDepreciated = $currentBookValue <= $asset->salvage_value;
        
        return [
            'accumulated_depreciation' => round($accumulatedDepreciation, 2),
            'current_book_value' => round($currentBookValue, 2),
            'annual_depreciation' => round($annualDepreciation, 2),
            'monthly_depreciation' => round($monthlyDepreciation, 2),
            'depreciation_rate' => round((1 / $asset->useful_life_years) * 100, 2),
            'is_fully_depreciated' => $isFullyDepreciated,
        ];
    }
    
    /**
     * Declining balance depreciation (accelerated)
     * Formula: Book Value * (Rate / Useful Life)
     */
    private function calculateDecliningBalance(Asset $asset, float $ageInYears): array
    {
        $rate = $asset->declining_balance_rate / $asset->useful_life_years;
        $bookValue = $asset->purchase_cost;
        $accumulatedDepreciation = 0;
        
        // Calculate year by year
        for ($year = 0; $year < $ageInYears; $year++) {
            $yearDepreciation = $bookValue * $rate;
            
            // Don't depreciate below salvage value
            if ($bookValue - $yearDepreciation < $asset->salvage_value) {
                $yearDepreciation = $bookValue - $asset->salvage_value;
            }
            
            $accumulatedDepreciation += $yearDepreciation;
            $bookValue -= $yearDepreciation;
            
            if ($bookValue <= $asset->salvage_value) {
                break;
            }
        }
        
        // Handle partial year
        $partialYear = $ageInYears - floor($ageInYears);
        if ($partialYear > 0 && $bookValue > $asset->salvage_value) {
            $partialYearDepreciation = $bookValue * $rate * $partialYear;
            if ($bookValue - $partialYearDepreciation < $asset->salvage_value) {
                $partialYearDepreciation = $bookValue - $asset->salvage_value;
            }
            $accumulatedDepreciation += $partialYearDepreciation;
            $bookValue -= $partialYearDepreciation;
        }
        
        $currentBookValue = max($bookValue, $asset->salvage_value);
        $annualDepreciation = $currentBookValue * $rate;
        $isFullyDepreciated = $currentBookValue <= $asset->salvage_value;
        
        return [
            'accumulated_depreciation' => round($accumulatedDepreciation, 2),
            'current_book_value' => round($currentBookValue, 2),
            'annual_depreciation' => round($annualDepreciation, 2),
            'monthly_depreciation' => round($annualDepreciation / 12, 2),
            'depreciation_rate' => round($rate * 100, 2),
            'is_fully_depreciated' => $isFullyDepreciated,
        ];
    }
    
    /**
     * Sum of years digits depreciation (accelerated)
     * Formula: (Cost - Salvage) * (Remaining Life / Sum of Years Digits)
     */
    private function calculateSumOfYearsDigits(Asset $asset, float $ageInYears): array
    {
        $depreciableAmount = $asset->purchase_cost - $asset->salvage_value;
        $usefulLife = (int) ceil($asset->useful_life_years);
        $sumOfYears = ($usefulLife * ($usefulLife + 1)) / 2;
        
        $accumulatedDepreciation = 0;
        $currentYear = min(ceil($ageInYears), $usefulLife);
        
        // Calculate accumulated depreciation for complete years
        for ($year = 1; $year <= floor($ageInYears) && $year <= $usefulLife; $year++) {
            $remainingLife = $usefulLife - $year + 1;
            $yearDepreciation = $depreciableAmount * ($remainingLife / $sumOfYears);
            $accumulatedDepreciation += $yearDepreciation;
        }
        
        // Handle partial year
        $partialYear = $ageInYears - floor($ageInYears);
        if ($partialYear > 0 && floor($ageInYears) < $usefulLife) {
            $year = floor($ageInYears) + 1;
            $remainingLife = $usefulLife - $year + 1;
            $yearDepreciation = $depreciableAmount * ($remainingLife / $sumOfYears);
            $accumulatedDepreciation += $yearDepreciation * $partialYear;
        }
        
        // Ensure we don't exceed depreciable amount
        $accumulatedDepreciation = min($accumulatedDepreciation, $depreciableAmount);
        
        $currentBookValue = max(
            $asset->purchase_cost - $accumulatedDepreciation,
            $asset->salvage_value
        );
        
        // Calculate current year depreciation
        $remainingLife = max(0, $usefulLife - floor($ageInYears));
        $annualDepreciation = $remainingLife > 0 
            ? $depreciableAmount * ($remainingLife / $sumOfYears)
            : 0;
        
        $isFullyDepreciated = $currentBookValue <= $asset->salvage_value;
        
        return [
            'accumulated_depreciation' => round($accumulatedDepreciation, 2),
            'current_book_value' => round($currentBookValue, 2),
            'annual_depreciation' => round($annualDepreciation, 2),
            'monthly_depreciation' => round($annualDepreciation / 12, 2),
            'depreciation_rate' => round(($remainingLife / $sumOfYears) * 100, 2),
            'is_fully_depreciated' => $isFullyDepreciated,
        ];
    }
    
    /**
     * Calculate depreciation schedule for entire useful life
     */
    public function calculateDepreciationSchedule(Asset $asset): array
    {
        $schedule = [];
        $startDate = $asset->depreciation_start_date 
            ? Carbon::parse($asset->depreciation_start_date)
            : Carbon::parse($asset->purchase_date);
        
        for ($year = 1; $year <= ceil($asset->useful_life_years); $year++) {
            $endOfYear = $startDate->copy()->addYears($year);
            $depreciation = $this->calculateDepreciation($asset, $endOfYear);
            
            $schedule[] = [
                'year' => $year,
                'date' => $endOfYear->format('Y-m-d'),
                'annual_depreciation' => $depreciation['annual_depreciation'],
                'accumulated_depreciation' => $depreciation['accumulated_depreciation'],
                'book_value' => $depreciation['current_book_value'],
            ];
            
            if ($depreciation['is_fully_depreciated']) {
                break;
            }
        }
        
        return $schedule;
    }
    
    /**
     * Get depreciation summary for multiple assets
     */
    public function getDepreciationSummary($assets): array
    {
        $totalPurchaseCost = 0;
        $totalAccumulatedDepreciation = 0;
        $totalCurrentBookValue = 0;
        $totalAnnualDepreciation = 0;
        $fullyDepreciatedCount = 0;
        
        foreach ($assets as $asset) {
            $depreciation = $this->calculateDepreciation($asset);
            
            $totalPurchaseCost += $depreciation['purchase_cost'];
            $totalAccumulatedDepreciation += $depreciation['accumulated_depreciation'];
            $totalCurrentBookValue += $depreciation['current_book_value'];
            $totalAnnualDepreciation += $depreciation['annual_depreciation'];
            
            if ($depreciation['is_fully_depreciated']) {
                $fullyDepreciatedCount++;
            }
        }
        
        return [
            'total_assets' => count($assets),
            'total_purchase_cost' => round($totalPurchaseCost, 2),
            'total_accumulated_depreciation' => round($totalAccumulatedDepreciation, 2),
            'total_current_book_value' => round($totalCurrentBookValue, 2),
            'total_annual_depreciation' => round($totalAnnualDepreciation, 2),
            'fully_depreciated_count' => $fullyDepreciatedCount,
            'average_depreciation_rate' => $totalPurchaseCost > 0 
                ? round(($totalAccumulatedDepreciation / $totalPurchaseCost) * 100, 2)
                : 0,
        ];
    }
}
