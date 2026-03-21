<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Warranty;
use App\Models\Semester;
use App\Services\NotificationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Carbon\Carbon;

class AssetsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use RemembersRowNumber;

    protected array $errors = [];
    protected int $successCount = 0;
    protected int $failedCount = 0;
    protected NotificationService $notificationService;
    protected int $currentRowNumber = 1;

    public function __construct()
    {
        $this->notificationService = app(NotificationService::class);
    }

    public function collection(Collection $rows)
    {
        // Load all categories for validation
        $categories = Category::pluck('id', 'name')->toArray();
        $validConditions = ['Excellent', 'Good', 'Fair', 'Poor'];
        $validDepreciationMethods = ['straight_line', 'declining_balance', 'sum_of_years_digits'];

        // Get current semester
        $currentSemester = Semester::current() ?? Semester::forDate(now());

        // FIRST PASS: Validate all rows and collect validated data
        $validatedRows = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // Excel row number (1-indexed, +1 for header)

            // Skip completely empty rows (check essential fields)
            if ($this->isRowEmpty($row)) {
                continue;
            }

            // Validate required fields
            $rowErrors = [];

            // Get category name and validate
            $categoryName = trim($row['category'] ?? '');
            if (empty($categoryName)) {
                $rowErrors[] = 'Category is required';
            } elseif (!isset($categories[$categoryName])) {
                $rowErrors[] = "Category '{$categoryName}' does not exist";
            }

            // Validate condition
            $condition = trim($row['condition'] ?? '');
            if (empty($condition)) {
                $rowErrors[] = 'Condition is required';
            } elseif (!in_array($condition, $validConditions)) {
                $rowErrors[] = "Condition must be one of: " . implode(', ', $validConditions);
            }

            // Validate purchase cost
            $purchaseCost = $row['purchase_cost'] ?? null;
            if (empty($purchaseCost) || !is_numeric($purchaseCost) || $purchaseCost < 0) {
                $rowErrors[] = 'Purchase Cost must be a valid positive number';
            }

            // Validate purchase date
            $purchaseDate = $this->parseDate($row['purchase_date'] ?? null);
            if (!$purchaseDate) {
                $rowErrors[] = 'Purchase Date is required and must be in MM-DD-YYYY format';
            }

            // Validate manufacturer
            $manufacturer = trim($row['manufacturer'] ?? '');
            if (empty($manufacturer)) {
                $rowErrors[] = 'Manufacturer is required';
            }

            // Validate model
            $model = trim($row['model'] ?? '');
            if (empty($model)) {
                $rowErrors[] = 'Model is required';
            }

            // Validate warranty expiry date
            $warrantyExpiry = $this->parseDate($row['warranty_expiry_date'] ?? null);
            if (!$warrantyExpiry) {
                $rowErrors[] = 'Warranty Expiry Date is required and must be in MM-DD-YYYY format';
            }

            // Validate depreciation method
            $depreciationMethod = $this->normalizeDepreciationMethod($row['depreciation_method'] ?? '');
            if (empty($depreciationMethod)) {
                $rowErrors[] = 'Depreciation Method is required';
            } elseif (!in_array($depreciationMethod, $validDepreciationMethods)) {
                $rowErrors[] = "Depreciation Method must be one of: Straight-Line, Declining Balance, Sum of Years Digits";
            }

            // Validate useful life
            $usefulLife = $row['useful_life'] ?? null;
            if (empty($usefulLife) || !is_numeric($usefulLife) || $usefulLife <= 0) {
                $rowErrors[] = 'Useful Life must be a valid positive number';
            }

            // Validate salvage value
            $salvageValue = $row['salvage_value'] ?? null;
            if (!isset($salvageValue) || !is_numeric($salvageValue) || $salvageValue < 0) {
                $rowErrors[] = 'Salvage Value must be a valid number (0 or greater)';
            }

            // Generate default name if not provided
            $assetName = trim($row['asset_name'] ?? '');
            if (empty($assetName)) {
                $assetName = $manufacturer . ' ' . $model;
            }

            // If there are errors, add them
            if (!empty($rowErrors)) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'errors' => $rowErrors
                ];
                $this->failedCount++;
            } else {
                // Store validated data for second pass
                $validatedRows[] = [
                    'row_number' => $rowNumber,
                    'category_name' => $categoryName,
                    'condition' => $condition,
                    'purchase_cost' => $purchaseCost,
                    'purchase_date' => $purchaseDate,
                    'manufacturer' => $manufacturer,
                    'model' => $model,
                    'warranty_expiry' => $warrantyExpiry,
                    'depreciation_method' => $depreciationMethod,
                    'useful_life' => $usefulLife,
                    'salvage_value' => $salvageValue,
                    'asset_name' => $assetName,
                    'description' => trim($row['description'] ?? ''),
                ];
            }
        }

        // If ANY row has errors, stop here - don't create any assets
        if (!empty($this->errors)) {
            return;
        }

        // No rows to import
        if (empty($validatedRows)) {
            return;
        }

        // SECOND PASS: Create all assets (only if no errors)
        // Use database transaction to ensure all-or-nothing
        DB::beginTransaction();

        try {
            foreach ($validatedRows as $data) {
                // Get category
                $category = Category::where('name', $data['category_name'])->first();

                // Generate asset code
                $latestAsset = Asset::where('category_id', $category->id)
                    ->where('asset_code', 'like', $category->code . '%')
                    ->orderByRaw('CAST(SUBSTRING(asset_code, -4) AS UNSIGNED) DESC')
                    ->first();

                $sequence = $latestAsset ? (int)substr($latestAsset->asset_code, -4) + 1 : 1;
                $assetCode = $category->code . str_pad($sequence, 4, '0', STR_PAD_LEFT);

                // Create the asset
                $asset = Asset::create([
                    'asset_code' => $assetCode,
                    'name' => $data['asset_name'],
                    'category_id' => $category->id,
                    'condition' => $data['condition'],
                    'description' => $data['description'],
                    'purchase_cost' => $data['purchase_cost'],
                    'purchase_date' => $data['purchase_date'],
                    'status' => 'Available',
                    'approval_status' => 'pending',
                    'created_by' => Auth::id(),
                    'registered_semester_id' => $currentSemester?->id,
                    'depreciation_method' => $data['depreciation_method'],
                    'useful_life_years' => $data['useful_life'],
                    'salvage_value' => $data['salvage_value'],
                    'declining_balance_rate' => 2,
                    'depreciation_start_date' => $data['purchase_date'],
                ]);

                // Create warranty record
                Warranty::create([
                    'asset_id' => $asset->id,
                    'manufacturer' => $data['manufacturer'],
                    'model' => $data['model'],
                    'warranty_expiry' => $data['warranty_expiry'],
                ]);

                // Notify admins
                $this->notificationService->notifyAdminsOfPendingAsset($asset);

                $this->successCount++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors[] = [
                'row' => 0,
                'errors' => ['Import failed: ' . $e->getMessage()]
            ];
            $this->failedCount = count($validatedRows);
            $this->successCount = 0;
        }
    }

    protected function isRowEmpty($row): bool
    {
        // Check if essential fields are all empty
        // If Category, Manufacturer, Model, AND Purchase Cost are all empty, consider row empty
        $essentialFields = [
            'category',
            'manufacturer',
            'model',
            'purchase_cost',
        ];

        foreach ($essentialFields as $field) {
            $value = $row[$field] ?? null;
            if (!empty($value) && trim((string)$value) !== '') {
                return false; // At least one essential field has data
            }
        }

        return true; // All essential fields are empty
    }

    protected function parseDate($value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        // If it's already a Carbon instance or DateTime
        if ($value instanceof \DateTime || $value instanceof Carbon) {
            return Carbon::parse($value);
        }

        // If it's a numeric value (Excel date serial number)
        // Excel serial dates: 1 = Jan 1, 1900. Modern dates are typically > 40000
        // We only accept Excel dates from year 2000 onwards (serial >= 36526)
        if (is_numeric($value)) {
            $numValue = (float) $value;
            // Only accept if it looks like a valid Excel date (year 2000+ = serial 36526+)
            if ($numValue >= 36526 && $numValue <= 73415) { // 2000 to 2100
                try {
                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($numValue);
                    return Carbon::createFromFormat('Y-m-d', $date->format('Y-m-d'));
                } catch (\Exception $e) {
                    return null;
                }
            }
            // If it's just a year like "2028", reject it
            return null;
        }

        // Convert to string and trim
        $value = trim((string) $value);

        // Strict validation: Only accept MM-DD-YYYY or MM/DD/YYYY format
        // Pattern: 2 digits, separator, 2 digits, separator, 4 digits
        if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $value, $matches)) {
            $month = (int) $matches[1];
            $day = (int) $matches[2];
            $year = (int) $matches[3];

            // Validate ranges
            if ($month < 1 || $month > 12) {
                return null;
            }
            if ($day < 1 || $day > 31) {
                return null;
            }
            if ($year < 1900 || $year > 2100) {
                return null;
            }

            // Check if it's a valid date
            if (!checkdate($month, $day, $year)) {
                return null;
            }

            try {
                return Carbon::createFromDate($year, $month, $day);
            } catch (\Exception $e) {
                return null;
            }
        }

        // Reject all other formats
        return null;
    }

    protected function normalizeDepreciationMethod(string $method): string
    {
        $method = strtolower(trim($method));

        $methodMap = [
            'straight-line' => 'straight_line',
            'straight line' => 'straight_line',
            'straightline' => 'straight_line',
            'straight_line' => 'straight_line',
            'declining balance' => 'declining_balance',
            'declining-balance' => 'declining_balance',
            'decliningbalance' => 'declining_balance',
            'declining_balance' => 'declining_balance',
            'sum of years digits' => 'sum_of_years_digits',
            'sum of years' => 'sum_of_years_digits',
            'sum-of-years-digits' => 'sum_of_years_digits',
            'sumofyearsdigits' => 'sum_of_years_digits',
            'sum_of_years_digits' => 'sum_of_years_digits',
            'soyd' => 'sum_of_years_digits',
        ];

        return $methodMap[$method] ?? '';
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getFailedCount(): int
    {
        return $this->failedCount;
    }
}
