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

class DisposalSummarySheet implements FromCollection, ShouldAutoSize, WithStyles, WithEvents, WithTitle
{
    protected Collection $disposals;

    public function __construct(Collection $disposals)
    {
        $this->disposals = $disposals;
    }

    public function title(): string
    {
        return 'Summary';
    }

    public function collection()
    {
        // Return empty collection as we'll manually build the summary
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
                $disposals = $this->disposals;

                // Calculate summary statistics
                $totalDisposals = $disposals->count();
                $byCategory = $disposals->groupBy(function($disposal) {
                    return $disposal->asset->category->name ?? 'Uncategorized';
                })->map->count()->sortDesc();

                $byBuilding = $disposals->groupBy(function($disposal) {
                    return $disposal->asset->location->building ?? 'Unknown';
                })->map->count()->sortDesc();

                $byMonth = $disposals->groupBy(function($disposal) {
                    return $disposal->disposal_date ? $disposal->disposal_date->format('F Y') : 'Unknown';
                })->map->count()->sortDesc();

                $byDisposedBy = $disposals->groupBy('disposed_by')->map->count()->sortDesc();

                $totalCost = $disposals->sum(function($disposal) {
                    return $disposal->asset->purchase_cost ?? 0;
                });

                $oldestDisposal = $disposals->min('disposal_date');
                $newestDisposal = $disposals->max('disposal_date');

                // Build the summary sheet
                $row = 1;

                // Title
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'DISPOSAL SUMMARY REPORT');
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
                $this->addStatRow($sheet, $row++, 'Total Disposal Records', $totalDisposals, 'number');
                $this->addStatRow($sheet, $row++, 'Total Asset Value Disposed', '₱' . number_format($totalCost, 2), 'currency');
                $this->addStatRow($sheet, $row++, 'Date Range', 
                    ($oldestDisposal ? \Carbon\Carbon::parse($oldestDisposal)->format('M d, Y') : 'N/A') . ' to ' . 
                    ($newestDisposal ? \Carbon\Carbon::parse($newestDisposal)->format('M d, Y') : 'N/A'), 'text');
                $this->addStatRow($sheet, $row++, 'Average Cost per Asset', $totalDisposals > 0 ? '₱' . number_format($totalCost / $totalDisposals, 2) : '₱0.00', 'currency');

                $row += 2;

                // By Category Section
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->setCellValue('A' . $row, '📁 DISPOSALS BY CATEGORY');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);

                $row++;
                $this->addTableHeader($sheet, $row++, ['Category', 'Count', 'Percentage']);
                foreach ($byCategory as $category => $count) {
                    $percentage = $totalDisposals > 0 ? ($count / $totalDisposals * 100) : 0;
                    $this->addTableRow($sheet, $row++, [$category, $count, number_format($percentage, 1) . '%']);
                }

                $row += 2;

                // By Building Section
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->setCellValue('A' . $row, '🏢 DISPOSALS BY BUILDING');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);

                $row++;
                $this->addTableHeader($sheet, $row++, ['Building', 'Count', 'Percentage']);
                foreach ($byBuilding as $building => $count) {
                    $percentage = $totalDisposals > 0 ? ($count / $totalDisposals * 100) : 0;
                    $this->addTableRow($sheet, $row++, [$building, $count, number_format($percentage, 1) . '%']);
                }

                $row += 2;

                // By Month Section
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->setCellValue('A' . $row, '📅 DISPOSALS BY MONTH');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7C3AED']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);

                $row++;
                $this->addTableHeader($sheet, $row++, ['Month', 'Count', 'Percentage']);
                foreach ($byMonth->take(12) as $month => $count) {
                    $percentage = $totalDisposals > 0 ? ($count / $totalDisposals * 100) : 0;
                    $this->addTableRow($sheet, $row++, [$month, $count, number_format($percentage, 1) . '%']);
                }

                $row += 2;

                // By Disposed By Section
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->setCellValue('A' . $row, '👤 DISPOSALS BY PERSON');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                $sheet->getRowDimension($row)->setRowHeight(28);

                $row++;
                $this->addTableHeader($sheet, $row++, ['Disposed By', 'Count', 'Percentage']);
                foreach ($byDisposedBy as $person => $count) {
                    $percentage = $totalDisposals > 0 ? ($count / $totalDisposals * 100) : 0;
                    $this->addTableRow($sheet, $row++, [$person, $count, number_format($percentage, 1) . '%']);
                }

                // Set minimum column widths for better appearance
                $sheet->getColumnDimension('C')->setWidth(15); // Percentage column
                $sheet->getColumnDimension('D')->setWidth(15); // Extra column if needed
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

        $sheet->getRowDimension($row)->setRowHeight(-1); // Auto-height
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
        $sheet->getRowDimension($row)->setRowHeight(-1); // Auto-height
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
        $sheet->getRowDimension($row)->setRowHeight(-1); // Auto-height
    }
}
