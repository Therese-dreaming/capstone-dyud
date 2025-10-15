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

class LostAssetsSummarySheet implements FromCollection, ShouldAutoSize, WithStyles, WithEvents, WithTitle
{
    protected Collection $lostAssets;

    public function __construct(Collection $lostAssets)
    {
        $this->lostAssets = $lostAssets;
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
                $lostAssets = $this->lostAssets;

                // Calculate summary statistics
                $totalLostAssets = $lostAssets->count();
                
                $byStatus = $lostAssets->groupBy('status')->map->count()->sortDesc();
                
                $byCategory = $lostAssets->groupBy(function($lostAsset) {
                    return $lostAsset->asset->category->name ?? 'Uncategorized';
                })->map->count()->sortDesc();

                $byBuilding = $lostAssets->groupBy(function($lostAsset) {
                    return $lostAsset->asset->location->building ?? 'Unknown';
                })->map->count()->sortDesc();

                $byMonth = $lostAssets->groupBy(function($lostAsset) {
                    return $lostAsset->reported_date ? $lostAsset->reported_date->format('F Y') : 'Unknown';
                })->map->count()->sortDesc();

                $byReporter = $lostAssets->groupBy(function($lostAsset) {
                    return $lostAsset->reportedBy->name ?? 'Unknown';
                })->map->count()->sortDesc();

                $investigating = $lostAssets->where('status', 'investigating')->count();
                $found = $lostAssets->where('status', 'found')->count();
                $permanentlyLost = $lostAssets->where('status', 'permanently_lost')->count();

                $oldestReport = $lostAssets->min('reported_date');
                $newestReport = $lostAssets->max('reported_date');

                // Build the summary sheet
                $row = 1;

                // Title
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'LOST ASSETS SUMMARY REPORT');
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
                $this->addStatRow($sheet, $row++, 'Total Lost Asset Reports', $totalLostAssets, 'number');
                $this->addStatRow($sheet, $row++, 'Under Investigation', $investigating, 'number');
                $this->addStatRow($sheet, $row++, 'Found', $found, 'number');
                $this->addStatRow($sheet, $row++, 'Permanently Lost', $permanentlyLost, 'number');
                $this->addStatRow($sheet, $row++, 'Date Range', 
                    ($oldestReport ? \Carbon\Carbon::parse($oldestReport)->format('M d, Y') : 'N/A') . ' to ' . 
                    ($newestReport ? \Carbon\Carbon::parse($newestReport)->format('M d, Y') : 'N/A'), 'text');

                $row += 2;

                // By Status Section
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->setCellValue('A' . $row, '📋 REPORTS BY STATUS');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);

                $row++;
                $this->addTableHeader($sheet, $row++, ['Status', 'Count', 'Percentage']);
                foreach ($byStatus as $status => $count) {
                    $percentage = $totalLostAssets > 0 ? ($count / $totalLostAssets * 100) : 0;
                    $statusLabel = ucfirst(str_replace('_', ' ', $status));
                    $this->addTableRow($sheet, $row++, [$statusLabel, $count, number_format($percentage, 1) . '%']);
                }

                $row += 2;

                // By Category Section
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->setCellValue('A' . $row, '📁 REPORTS BY CATEGORY');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);

                $row++;
                $this->addTableHeader($sheet, $row++, ['Category', 'Count', 'Percentage']);
                foreach ($byCategory as $category => $count) {
                    $percentage = $totalLostAssets > 0 ? ($count / $totalLostAssets * 100) : 0;
                    $this->addTableRow($sheet, $row++, [$category, $count, number_format($percentage, 1) . '%']);
                }

                $row += 2;

                // By Building Section
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->setCellValue('A' . $row, '🏢 REPORTS BY BUILDING');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);

                $row++;
                $this->addTableHeader($sheet, $row++, ['Building', 'Count', 'Percentage']);
                foreach ($byBuilding as $building => $count) {
                    $percentage = $totalLostAssets > 0 ? ($count / $totalLostAssets * 100) : 0;
                    $this->addTableRow($sheet, $row++, [$building, $count, number_format($percentage, 1) . '%']);
                }

                $row += 2;

                // By Month Section
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->setCellValue('A' . $row, '📅 REPORTS BY MONTH');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);

                $row++;
                $this->addTableHeader($sheet, $row++, ['Month', 'Count', 'Percentage']);
                foreach ($byMonth->take(12) as $month => $count) {
                    $percentage = $totalLostAssets > 0 ? ($count / $totalLostAssets * 100) : 0;
                    $this->addTableRow($sheet, $row++, [$month, $count, number_format($percentage, 1) . '%']);
                }

                $row += 2;

                // By Reporter Section
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->setCellValue('A' . $row, '👤 REPORTS BY PERSON');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EA580C']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);

                $row++;
                $this->addTableHeader($sheet, $row++, ['Reported By', 'Count', 'Percentage']);
                foreach ($byReporter as $person => $count) {
                    $percentage = $totalLostAssets > 0 ? ($count / $totalLostAssets * 100) : 0;
                    $this->addTableRow($sheet, $row++, [$person, $count, number_format($percentage, 1) . '%']);
                }

                // Set minimum column widths
                $sheet->getColumnDimension('C')->setWidth(15); // Percentage column
                $sheet->getColumnDimension('D')->setWidth(15);
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
