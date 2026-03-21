<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssetImportReferenceSheet implements FromArray, ShouldAutoSize, WithStyles, WithEvents, WithTitle
{
    public function title(): string
    {
        return 'Reference';
    }

    public function array(): array
    {
        $data = [];

        // Field Descriptions section
        $data[] = ['FIELD DESCRIPTIONS', '', ''];
        $data[] = ['Field Name', 'Required', 'Description'];
        $data[] = ['Asset Name', 'Optional', 'Name of the asset. If left blank, will be auto-generated from Manufacturer + Model.'];
        $data[] = ['Category', 'Yes', 'Select from the dropdown. Must match an existing category.'];
        $data[] = ['Condition', 'Yes', 'Select from: Excellent, Good, Fair, Poor'];
        $data[] = ['Purchase Cost', 'Yes', 'Decimal number (e.g., 45000.00). Must be 0 or greater.'];
        $data[] = ['Purchase Date', 'Yes', 'Date format: MM-DD-YYYY (e.g., 03-21-2026)'];
        $data[] = ['Manufacturer', 'Yes', 'Brand/manufacturer of the asset (e.g., Dell, HP, Canon)'];
        $data[] = ['Model', 'Yes', 'Model number or name (e.g., Latitude 5520, LaserJet Pro)'];
        $data[] = ['Warranty Expiry Date', 'Yes', 'Date format: MM-DD-YYYY (e.g., 03-21-2028)'];
        $data[] = ['Depreciation Method', 'Yes', 'Select from: Straight-Line, Declining Balance, Sum of Years Digits'];
        $data[] = ['Useful Life', 'Yes', 'Whole number representing years (e.g., 5)'];
        $data[] = ['Salvage Value', 'Yes', 'Decimal number (e.g., 5000.00). Can be 0.'];
        $data[] = ['Description', 'Optional', 'Additional details about the asset'];

        $data[] = ['', '', ''];
        $data[] = ['', '', ''];

        // Available Categories section
        $data[] = ['AVAILABLE CATEGORIES', '', ''];
        $data[] = ['Category Name', 'Category Code', 'Description'];

        $categories = Category::orderBy('name')->get();
        foreach ($categories as $category) {
            $data[] = [$category->name, $category->code, 'Used for asset code generation'];
        }

        $data[] = ['', '', ''];
        $data[] = ['', '', ''];

        // Depreciation Methods section
        $data[] = ['DEPRECIATION METHODS', '', ''];
        $data[] = ['Method', 'Description', ''];
        $data[] = ['Straight-Line', 'Equal depreciation each year: (Cost - Salvage) / Useful Life', ''];
        $data[] = ['Declining Balance', 'Higher depreciation in early years, decreasing over time', ''];
        $data[] = ['Sum of Years Digits', 'Accelerated depreciation based on remaining useful life', ''];

        $data[] = ['', '', ''];
        $data[] = ['', '', ''];

        // Condition Options section
        $data[] = ['CONDITION OPTIONS', '', ''];
        $data[] = ['Condition', 'Description', ''];
        $data[] = ['Excellent', 'Brand new or like-new condition', ''];
        $data[] = ['Good', 'Minor wear but fully functional', ''];
        $data[] = ['Fair', 'Noticeable wear but still usable', ''];
        $data[] = ['Poor', 'Significant wear, may need repair', ''];

        return $data;
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

                // Style section headers
                $sectionRows = [1, 17, 24, 30];
                $categories = Category::count();

                // Recalculate row positions based on category count
                $fieldDescEnd = 13;
                $categoriesStart = 16;
                $categoriesHeaderRow = $categoriesStart + 1;
                $categoriesEnd = $categoriesHeaderRow + $categories + 1;
                $depMethodsStart = $categoriesEnd + 2;
                $conditionStart = $depMethodsStart + 6;

                // Field Descriptions header
                $sheet->mergeCells('A1:C1');
                $sheet->getStyle('A1:C1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // Field descriptions table header
                $sheet->getStyle('A2:C2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '374151']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]]
                ]);

                // Field descriptions data
                $sheet->getStyle('A3:C14')->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]]
                ]);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(70);

                // Highlight required fields
                for ($row = 3; $row <= 14; $row++) {
                    $required = $sheet->getCell('B' . $row)->getValue();
                    if ($required === 'Yes') {
                        $sheet->getStyle('B' . $row)->applyFromArray([
                            'font' => ['color' => ['rgb' => 'DC2626']]
                        ]);
                    }
                }

                // Style section headers dynamically
                $currentRow = 17;
                $sheet->mergeCells('A' . $currentRow . ':C' . $currentRow);
                $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']], // Green
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
                $sheet->getRowDimension($currentRow)->setRowHeight(30);

                // Categories table header
                $currentRow++;
                $sheet->getStyle('A' . $currentRow . ':C' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '374151']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '6EE7B7']]]
                ]);
            }
        ];
    }
}
