<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DepreciationSummarySheet implements FromCollection, ShouldAutoSize, WithStyles, WithEvents, WithTitle
{
    protected Collection $assets;
    protected array $summary;

    public function __construct(Collection $assets, array $summary)
    {
        $this->assets = $assets;
        $this->summary = $summary;
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function collection()
    {
        return collect([]);
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
                $summary = $this->summary;
                $assets = $this->assets;

                // Group data for analysis
                $byCategory = $assets->groupBy(function($asset) {
                    return $asset->category->name ?? 'Uncategorized';
                });

                $byMethod = $assets->groupBy('depreciation_method');

                $byBuilding = $assets->groupBy(function($asset) {
                    return $asset->location->building ?? 'Unknown';
                });

                // Build the summary sheet
                $row = 1;

                // Title
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'DEPRECIATION SUMMARY REPORT');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 20, 'color' => ['rgb' => '800000'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension(1)->setRowHeight(36);

                // Generated date
                $row = 2;
                $sheet->mergeCells('A2:D2');
                $sheet->setCellValue('A2', 'Generated on ' . now()->format('F d, Y \a\t g:i A'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 11, 'color' => ['rgb' => '666666'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension(2)->setRowHeight(20);

                $row = 4;

                // Overall Statistics Section
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->setCellValue('A' . $row, '📊 OVERALL STATISTICS');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);

                $row++;
                $this->addStatRow($sheet, $row++, 'Total Assets', $summary['total_assets'], 'number');
                $this->addStatRow($sheet, $row++, 'Total Purchase Cost', '₱' . number_format($summary['total_purchase_cost'], 2), 'currency');
                $this->addStatRow($sheet, $row++, 'Total Current Book Value', '₱' . number_format($summary['total_current_book_value'], 2), 'currency');
                $this->addStatRow($sheet, $row++, 'Total Accumulated Depreciation', '₱' . number_format($summary['total_accumulated_depreciation'], 2), 'currency');
                $this->addStatRow($sheet, $row++, 'Total Annual Depreciation', '₱' . number_format($summary['total_annual_depreciation'], 2), 'currency');
                $this->addStatRow($sheet, $row++, 'Average Depreciation Rate', $summary['average_depreciation_rate'] . '%', 'text');
                $this->addStatRow($sheet, $row++, 'Fully Depreciated Assets', $summary['fully_depreciated_count'] . ' / ' . $summary['total_assets'], 'text');

                $row += 2;

                // By Category Section
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->setCellValue('A' . $row, '📁 DEPRECIATION BY CATEGORY');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);

                $row++;
                $this->addTableHeader($sheet, $row++, ['Category', 'Asset Count', 'Total Book Value', 'Total Depreciation']);
                foreach ($byCategory as $category => $categoryAssets) {
                    $count = $categoryAssets->count();
                    $bookValue = $categoryAssets->sum(function($asset) {
                        $dep = app(\App\Services\DepreciationService::class)->calculateDepreciation($asset);
                        return $dep['current_book_value'];
                    });
                    $depreciation = $categoryAssets->sum(function($asset) {
                        $dep = app(\App\Services\DepreciationService::class)->calculateDepreciation($asset);
                        return $dep['accumulated_depreciation'];
                    });
                    $this->addTableRow($sheet, $row++, [
                        $category, 
                        $count, 
                        '₱' . number_format($bookValue, 2),
                        '₱' . number_format($depreciation, 2)
                    ]);
                }

                $row += 2;

                // By Method Section
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->setCellValue('A' . $row, '🧮 DEPRECIATION BY METHOD');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);

                $row++;
                $this->addTableHeader($sheet, $row++, ['Method', 'Asset Count', 'Percentage']);
                $totalAssets = $summary['total_assets'];
                foreach ($byMethod as $method => $methodAssets) {
                    $count = $methodAssets->count();
                    $percentage = $totalAssets > 0 ? ($count / $totalAssets * 100) : 0;
                    $methodLabel = $methodAssets->first()->getDepreciationMethodLabel();
                    $this->addTableRow($sheet, $row++, [
                        $methodLabel, 
                        $count, 
                        number_format($percentage, 1) . '%'
                    ]);
                }

                $row += 2;

                // By Building Section
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->setCellValue('A' . $row, '🏢 DEPRECIATION BY BUILDING');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);

                $row++;
                $this->addTableHeader($sheet, $row++, ['Building', 'Asset Count', 'Total Book Value', 'Total Depreciation']);
                foreach ($byBuilding as $building => $buildingAssets) {
                    $count = $buildingAssets->count();
                    $bookValue = $buildingAssets->sum(function($asset) {
                        $dep = app(\App\Services\DepreciationService::class)->calculateDepreciation($asset);
                        return $dep['current_book_value'];
                    });
                    $depreciation = $buildingAssets->sum(function($asset) {
                        $dep = app(\App\Services\DepreciationService::class)->calculateDepreciation($asset);
                        return $dep['accumulated_depreciation'];
                    });
                    $this->addTableRow($sheet, $row++, [
                        $building, 
                        $count, 
                        '₱' . number_format($bookValue, 2),
                        '₱' . number_format($depreciation, 2)
                    ]);
                }

                // Set minimum column widths
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(18);
            }
        ];
    }

    private function addStatRow($sheet, $row, $label, $value, $type = 'text')
    {
        $sheet->setCellValue('A' . $row, $label);
        $sheet->mergeCells('B' . $row . ':D' . $row);
        $sheet->setCellValue('B' . $row, $value);

        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'name' => 'Arial'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]]
        ]);

        $sheet->getStyle('B' . $row . ':D' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'name' => 'Arial', 'color' => ['rgb' => $type === 'number' ? '800000' : '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]]
        ]);

        $sheet->getRowDimension($row)->setRowHeight(-1);
    }

    private function addTableHeader($sheet, $row, $headers)
    {
        $columns = ['A', 'B', 'C', 'D'];
        foreach ($headers as $index => $header) {
            $col = $columns[$index];
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]]
            ]);
        }
        $sheet->getRowDimension($row)->setRowHeight(-1);
    }

    private function addTableRow($sheet, $row, $values)
    {
        $columns = ['A', 'B', 'C', 'D'];
        foreach ($values as $index => $value) {
            $col = $columns[$index];
            $sheet->setCellValue($col . $row, $value);
            
            $alignment = $index === 0 ? Alignment::HORIZONTAL_LEFT : Alignment::HORIZONTAL_CENTER;
            
            $fillColor = $row % 2 == 0 ? 'FFFFFF' : 'F9FAFB';
            
            $sheet->getStyle($col . $row)->applyFromArray([
                'font' => ['size' => 10, 'name' => 'Arial'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
                'alignment' => ['horizontal' => $alignment, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]]
            ]);
        }
        $sheet->getRowDimension($row)->setRowHeight(-1);
    }
}
