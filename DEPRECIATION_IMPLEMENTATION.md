# Asset Depreciation System Implementation

## Overview
Comprehensive asset depreciation tracking system with multiple calculation methods, leveraging the existing warranties table for useful life estimation.

## Features Implemented

### 1. **Multiple Depreciation Methods**
- **Straight-Line**: Equal depreciation each year (most common)
- **Declining Balance**: Accelerated depreciation with higher amounts in early years
- **Sum of Years Digits**: Accelerated depreciation with gradually decreasing amounts

### 2. **Database Structure**
**Migration**: `2025_10_15_000001_add_depreciation_fields_to_assets_table.php`

New fields added to `assets` table:
- `depreciation_method` - Method used (straight_line, declining_balance, sum_of_years_digits)
- `useful_life_years` - Expected lifespan in years (default: 5)
- `salvage_value` - Residual value at end of useful life (default: 0)
- `declining_balance_rate` - Rate multiplier for declining balance (default: 2 for double declining)
- `depreciation_start_date` - When depreciation begins (defaults to purchase_date)

### 3. **Core Components**

#### **DepreciationService** (`app/Services/DepreciationService.php`)
Handles all depreciation calculations:
- `calculateDepreciation()` - Calculate current depreciation for an asset
- `calculateStraightLine()` - Straight-line method calculation
- `calculateDecliningBalance()` - Declining balance method calculation
- `calculateSumOfYearsDigits()` - Sum of years digits calculation
- `calculateDepreciationSchedule()` - Generate full depreciation schedule
- `getDepreciationSummary()` - Aggregate statistics for multiple assets

#### **Asset Model Updates** (`app/Models/Asset.php`)
New helper methods:
- `getDepreciation()` - Get depreciation calculation
- `getCurrentBookValue()` - Get current book value
- `getAccumulatedDepreciation()` - Get total depreciation to date
- `isFullyDepreciated()` - Check if asset is fully depreciated
- `getDepreciationSchedule()` - Get year-by-year schedule
- `getDepreciationMethodLabel()` - Get human-readable method name

#### **DepreciationController** (`app/Http/Controllers/DepreciationController.php`)
Routes and actions:
- `index()` - Main depreciation report with filters
- `show()` - Detailed depreciation for specific asset
- `edit()` - Edit depreciation settings
- `update()` - Save depreciation settings
- `export()` - Export report to CSV
- `byCategory()` - API endpoint for category breakdown
- `trend()` - API endpoint for trend data

### 4. **User Interface**

#### **Main Report** (`resources/views/depreciation/index.blade.php`)
- Summary cards showing total assets, purchase cost, book value, and accumulated depreciation
- Comprehensive filters (category, location, method, status)
- Detailed asset table with depreciation metrics
- Visual progress bars showing depreciation percentage
- Export to CSV functionality

#### **Asset Details** (`resources/views/depreciation/show.blade.php`)
- Complete asset and purchase information
- Current depreciation status cards
- Visual depreciation progress bar
- Full depreciation schedule table
- Year-by-year breakdown

#### **Settings Editor** (`resources/views/depreciation/edit.blade.php`)
- Edit depreciation method
- Adjust useful life and salvage value
- Set declining balance rate
- Modify depreciation start date
- Method descriptions and help text

### 5. **Integration Points**

#### **Asset Creation Form** (`resources/views/purchasing/assets/create.blade.php`)
Added depreciation settings section:
- Depreciation method selector
- Useful life input (default: 5 years)
- Salvage value input (default: 0)
- Informational help text

#### **Asset Show View** (`resources/views/assets/show.blade.php`)
Added depreciation information card showing:
- Current book value
- Accumulated depreciation
- Depreciation method and rate
- Useful life and age
- Annual/monthly depreciation
- Link to full depreciation details

#### **Dashboard** (`resources/views/dashboard/dashboard.blade.php`)
Added "Depreciation Report" quick action card

#### **PurchasingController** (`app/Http/Controllers/PurchasingController.php`)
Updated to handle depreciation fields:
- Validation for depreciation inputs
- Auto-set depreciation_start_date to purchase_date
- Default declining_balance_rate to 2

### 6. **Routes** (`routes/web.php`)
```php
// Admin-only depreciation routes
Route::get('/depreciation', [DepreciationController::class, 'index'])->name('depreciation.index');
Route::get('/depreciation/export', [DepreciationController::class, 'export'])->name('depreciation.export');
Route::get('/depreciation/{asset}', [DepreciationController::class, 'show'])->name('depreciation.show');
Route::get('/depreciation/{asset}/edit', [DepreciationController::class, 'edit'])->name('depreciation.edit');
Route::put('/depreciation/{asset}', [DepreciationController::class, 'update'])->name('depreciation.update');
Route::get('/api/depreciation/by-category', [DepreciationController::class, 'byCategory'])->name('api.depreciation.by-category');
Route::get('/api/depreciation/trend', [DepreciationController::class, 'trend'])->name('api.depreciation.trend');
```

## Depreciation Calculations

### Straight-Line Method
```
Annual Depreciation = (Purchase Cost - Salvage Value) / Useful Life
Book Value = Purchase Cost - (Annual Depreciation × Age)
```

### Declining Balance Method
```
Depreciation Rate = Declining Balance Rate / Useful Life
Annual Depreciation = Book Value × Depreciation Rate
Book Value = Previous Book Value - Annual Depreciation
```

### Sum of Years Digits Method
```
Sum of Years = (Useful Life × (Useful Life + 1)) / 2
Remaining Life = Useful Life - Current Year + 1
Annual Depreciation = (Purchase Cost - Salvage Value) × (Remaining Life / Sum of Years)
```

## Usage Examples

### Calculate Current Depreciation
```php
$asset = Asset::find(1);
$depreciation = $asset->getDepreciation();

// Returns array with:
// - purchase_cost
// - salvage_value
// - useful_life_years
// - age_years
// - accumulated_depreciation
// - current_book_value
// - annual_depreciation
// - monthly_depreciation
// - depreciation_rate
// - is_fully_depreciated
// - remaining_useful_life_years
```

### Get Depreciation Schedule
```php
$schedule = $asset->getDepreciationSchedule();

// Returns array of years with:
// - year (1, 2, 3, etc.)
// - date (end of year date)
// - annual_depreciation
// - accumulated_depreciation
// - book_value
```

### Check if Fully Depreciated
```php
if ($asset->isFullyDepreciated()) {
    // Asset has reached salvage value
}
```

## Setup Instructions

1. **Run Migration**
   ```bash
   php artisan migrate
   ```

2. **Update Existing Assets** (Optional)
   If you have existing assets, you may want to set default depreciation values:
   ```php
   Asset::whereNull('depreciation_method')->update([
       'depreciation_method' => 'straight_line',
       'useful_life_years' => 5,
       'salvage_value' => 0,
       'declining_balance_rate' => 2,
   ]);
   ```

3. **Access Depreciation Reports**
   - Navigate to Admin Dashboard
   - Click "Depreciation Report" quick action
   - Or visit: `/depreciation`

## Benefits

1. **Financial Accuracy**: Track true asset values over time
2. **Compliance**: Meet accounting and reporting requirements
3. **Decision Making**: Understand when assets need replacement
4. **Budget Planning**: Forecast depreciation expenses
5. **Stakeholder Reporting**: Generate comprehensive depreciation reports
6. **Flexibility**: Support multiple depreciation methods
7. **Integration**: Seamlessly works with existing warranty system

## Relationship with Warranties Table

The warranties table complements depreciation tracking:
- **Warranty Period**: Often correlates with useful life
- **Manufacturer Data**: Helps estimate asset longevity
- **Warranty Expiry**: May indicate when depreciation accelerates
- **Combined Reporting**: Both warranty status and depreciation visible in asset details

## Future Enhancements (Optional)

1. **Category-Based Defaults**: Set default useful life per category
2. **Automatic Adjustments**: Auto-adjust based on maintenance history
3. **Depreciation Alerts**: Notify when assets are fully depreciated
4. **Tax Depreciation**: Support tax-specific depreciation methods
5. **Revaluation**: Allow asset revaluation and depreciation recalculation
6. **Disposal Integration**: Automatically calculate gain/loss on disposal

## Technical Notes

- All monetary values use `decimal(12,2)` for precision
- Depreciation calculations handle partial years
- Salvage value is enforced as minimum book value
- Service layer separates business logic from controllers
- All views use consistent styling with existing system
- Fully integrated with existing asset approval workflow
