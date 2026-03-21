<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssetImportInstructionsSheet implements FromArray, WithStyles, WithEvents, WithTitle, WithColumnWidths
{
    public function title(): string
    {
        return 'Instructions';
    }

    public function array(): array
    {
        return [
            ['ASSET IMPORT INSTRUCTIONS'],
            [''],
            ['Welcome! This template allows you to bulk import multiple assets at once.'],
            ['Please follow the steps below carefully to ensure successful import.'],
            [''],
            [''],
            ['STEP-BY-STEP GUIDE'],
            [''],
            ['Step 1: Review the Reference Sheet'],
            ['- Go to the "Reference" sheet to see all available categories'],
            ['- Review the field descriptions and requirements'],
            ['- Note which fields are required vs optional'],
            [''],
            ['Step 2: Fill in the Asset Import Sheet'],
            ['- Go to the "Asset Import" sheet'],
            ['- Delete the sample data rows (rows 2-3)'],
            ['- Enter your asset data starting from row 2'],
            ['- Use the dropdown selections for Category, Condition, and Depreciation Method'],
            [''],
            ['Step 3: Save and Upload'],
            ['- Save the file (keep the .xlsx format)'],
            ['- Go to the Asset Registration page'],
            ['- Click "Import from Excel" and upload this file'],
            [''],
            [''],
            ['IMPORTANT NOTES'],
            [''],
            ['Date Format:'],
            ['- Use MM-DD-YYYY format (e.g., 03-21-2026)'],
            ['- Both Purchase Date and Warranty Expiry Date must follow this format'],
            [''],
            ['Number Format:'],
            ['- Purchase Cost and Salvage Value should be decimal numbers (e.g., 45000.00)'],
            ['- Useful Life should be a whole number representing years (e.g., 5)'],
            [''],
            ['Dropdown Fields:'],
            ['- Category: Must match an existing category from your system'],
            ['- Condition: Choose from Excellent, Good, Fair, or Poor'],
            ['- Depreciation Method: Choose from Straight-Line, Declining Balance, or Sum of Years Digits'],
            [''],
            ['Optional Fields:'],
            ['- Asset Name: If left blank, will be auto-generated from Manufacturer + Model'],
            ['- Description: Additional details about the asset (optional)'],
            [''],
            [''],
            ['COMMON ERRORS AND SOLUTIONS'],
            [''],
            ['Error: "Category does not exist"'],
            ['Solution: Check the Reference sheet for valid category names. Make sure spelling matches exactly.'],
            [''],
            ['Error: "Date format invalid"'],
            ['Solution: Use MM-DD-YYYY format. Example: 03-21-2026 for March 21, 2026.'],
            [''],
            ['Error: "Required field missing"'],
            ['Solution: Make sure all required fields are filled. See Reference sheet for required fields.'],
            [''],
            [''],
            ['NEED HELP?'],
            [''],
            ['If you encounter issues during import, the system will show you which rows failed'],
            ['and the specific errors for each row. Fix the issues and try importing again.'],
            [''],
            ['For additional assistance, contact your system administrator.'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 100,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $delegate = $sheet->getDelegate();

                // Main title styling (Row 1)
                $sheet->mergeCells('A1:A1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 20, 'color' => ['rgb' => '7C3AED']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension(1)->setRowHeight(40);

                // Welcome text styling
                $sheet->getStyle('A3:A4')->applyFromArray([
                    'font' => ['size' => 12, 'color' => ['rgb' => '374151']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Section headers
                $sectionRows = [7, 26, 46, 58];
                foreach ($sectionRows as $row) {
                    $sheet->getStyle('A' . $row)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(30);
                }

                // Step headers (bold purple text)
                $stepRows = [9, 14, 19];
                foreach ($stepRows as $row) {
                    $sheet->getStyle('A' . $row)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '7C3AED']]
                    ]);
                }

                // Sub-section headers (bold)
                $subHeaderRows = [28, 32, 36, 41, 48, 51, 54];
                foreach ($subHeaderRows as $row) {
                    $sheet->getStyle('A' . $row)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']]
                    ]);
                }

                // Solution text (green)
                $solutionRows = [49, 52, 55];
                foreach ($solutionRows as $row) {
                    $sheet->getStyle('A' . $row)->applyFromArray([
                        'font' => ['color' => ['rgb' => '059669']]
                    ]);
                }

                // Bullet points styling (indent with gray text)
                $bulletRows = [10, 11, 12, 15, 16, 17, 18, 20, 21, 22, 29, 30, 33, 34, 37, 38, 39, 42, 43, 60, 61];
                foreach ($bulletRows as $row) {
                    $sheet->getStyle('A' . $row)->applyFromArray([
                        'font' => ['size' => 11, 'color' => ['rgb' => '4B5563']],
                        'alignment' => ['indent' => 2]
                    ]);
                }

                // Add a subtle border around the content area
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A1:A' . $highestRow)->applyFromArray([
                    'alignment' => ['wrapText' => true]
                ]);

                // Set print area and page setup
                $delegate->getPageSetup()->setFitToWidth(1);
                $delegate->getPageSetup()->setFitToHeight(0);

                // Freeze pane at row 2
                $sheet->freezePane('A2');
            }
        ];
    }
}
