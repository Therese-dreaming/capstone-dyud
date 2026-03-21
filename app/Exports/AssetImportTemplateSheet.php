<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class AssetImportTemplateSheet implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithEvents, WithTitle, WithColumnWidths
{
    public function title(): string
    {
        return 'Asset Import';
    }

    public function headings(): array
    {
        return [
            'Asset Name',
            'Category',
            'Condition',
            'Purchase Cost',
            'Purchase Date',
            'Manufacturer',
            'Model',
            'Warranty Expiry Date',
            'Depreciation Method',
            'Useful Life',
            'Salvage Value',
            'Description',
        ];
    }

    public function array(): array
    {
        // Return sample data rows
        return [
            [
                'Dell Laptop Latitude 5520',
                'Laptop',
                'Excellent',
                '45000.00',
                '03-21-2026',
                'Dell',
                'Latitude 5520',
                '03-21-2028',
                'Straight-Line',
                '5',
                '5000.00',
                'Intel Core i5, 8GB RAM, 256GB SSD',
            ],
            [
                'HP LaserJet Pro',
                'Printer',
                'Good',
                '25000.00',
                '03-21-2026',
                'HP',
                'LaserJet Pro M404dn',
                '03-21-2027',
                'Declining Balance',
                '3',
                '2500.00',
                'Monochrome laser printer',
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,  // Asset Name
            'B' => 18,  // Category
            'C' => 12,  // Condition
            'D' => 15,  // Purchase Cost
            'E' => 15,  // Purchase Date
            'F' => 18,  // Manufacturer
            'G' => 22,  // Model
            'H' => 20,  // Warranty Expiry Date
            'I' => 22,  // Depreciation Method
            'J' => 12,  // Useful Life
            'K' => 15,  // Salvage Value
            'L' => 40,  // Description
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']], // Purple
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $delegate = $sheet->getDelegate();

                // Get categories for dropdown
                $categories = Category::orderBy('name')->pluck('name')->toArray();
                $categoryList = implode(',', $categories);

                // Dropdown options
                $conditions = 'Excellent,Good,Fair,Poor';
                $depreciationMethods = 'Straight-Line,Declining Balance,Sum of Years Digits';

                // Apply dropdowns to rows 2-100
                for ($row = 2; $row <= 100; $row++) {
                    // Category dropdown (Column B)
                    $categoryValidation = $delegate->getCell('B' . $row)->getDataValidation();
                    $categoryValidation->setType(DataValidation::TYPE_LIST);
                    $categoryValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $categoryValidation->setAllowBlank(false);
                    $categoryValidation->setShowInputMessage(true);
                    $categoryValidation->setShowErrorMessage(true);
                    $categoryValidation->setShowDropDown(true);
                    $categoryValidation->setErrorTitle('Invalid Category');
                    $categoryValidation->setError('Please select a category from the list.');
                    $categoryValidation->setPromptTitle('Category');
                    $categoryValidation->setPrompt('Select a category from the dropdown.');
                    $categoryValidation->setFormula1('"' . $categoryList . '"');

                    // Condition dropdown (Column C)
                    $conditionValidation = $delegate->getCell('C' . $row)->getDataValidation();
                    $conditionValidation->setType(DataValidation::TYPE_LIST);
                    $conditionValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $conditionValidation->setAllowBlank(false);
                    $conditionValidation->setShowInputMessage(true);
                    $conditionValidation->setShowErrorMessage(true);
                    $conditionValidation->setShowDropDown(true);
                    $conditionValidation->setErrorTitle('Invalid Condition');
                    $conditionValidation->setError('Please select a condition from the list.');
                    $conditionValidation->setPromptTitle('Condition');
                    $conditionValidation->setPrompt('Select the asset condition.');
                    $conditionValidation->setFormula1('"' . $conditions . '"');

                    // Depreciation Method dropdown (Column I)
                    $depMethodValidation = $delegate->getCell('I' . $row)->getDataValidation();
                    $depMethodValidation->setType(DataValidation::TYPE_LIST);
                    $depMethodValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $depMethodValidation->setAllowBlank(false);
                    $depMethodValidation->setShowInputMessage(true);
                    $depMethodValidation->setShowErrorMessage(true);
                    $depMethodValidation->setShowDropDown(true);
                    $depMethodValidation->setErrorTitle('Invalid Method');
                    $depMethodValidation->setError('Please select a depreciation method from the list.');
                    $depMethodValidation->setPromptTitle('Depreciation Method');
                    $depMethodValidation->setPrompt('Select the depreciation method.');
                    $depMethodValidation->setFormula1('"' . $depreciationMethods . '"');
                }

                // Style header row
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getStyle('A1:L1')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '5B21B6']]
                    ]
                ]);

                // Style sample data rows
                $sheet->getStyle('A2:L3')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F3FF']], // Light purple
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]
                    ]
                ]);

                // Add a quick note
                $sheet->mergeCells('A5:L5');
                $sheet->setCellValue('A5', 'Note: Delete sample rows 2-3 before entering your data. See "Instructions" sheet for detailed guide.');
                $sheet->getStyle('A5')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '6B7280']]
                ]);

                // Freeze header row
                $sheet->freezePane('A2');
            }
        ];
    }
}
