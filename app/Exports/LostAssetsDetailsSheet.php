<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class LostAssetsDetailsSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents, WithTitle
{
    protected Collection $lostAssets;

    public function __construct(Collection $lostAssets)
    {
        $this->lostAssets = $lostAssets;
    }

    public function title(): string
    {
        return 'Lost Assets Details';
    }

    public function collection()
    {
        return $this->lostAssets;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Asset Code',
            'Asset Name',
            'Category',
            'Building',
            'Floor',
            'Room',
            'Reported By',
            'Reported Date',
            'Last Known Location',
            'Description',
            'Status',
            'Found Date',
            'Found Location',
            'Found Notes',
        ];
    }

    public function map($lostAsset): array
    {
        static $rowIndex = 0;
        $rowIndex++;

        $location = $lostAsset->asset?->location;
        $asset = $lostAsset->asset;

        return [
            $rowIndex,
            $asset->asset_code ?? 'N/A',
            $asset->name ?? 'N/A',
            $asset->category->name ?? 'N/A',
            $location->building ?? 'N/A',
            $location->floor ?? 'N/A',
            $location->room ?? 'N/A',
            $lostAsset->reportedBy->name ?? 'N/A',
            $lostAsset->reported_date ? $lostAsset->reported_date->format('M d, Y') : 'N/A',
            $lostAsset->last_known_location ?? 'N/A',
            $lostAsset->description ?? 'N/A',
            $lostAsset->getStatusLabel(),
            $lostAsset->found_date ? $lostAsset->found_date->format('M d, Y') : '',
            $lostAsset->found_location ?? '',
            $lostAsset->found_notes ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '800000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Insert title and subtitle (add extra rows for logo)
                $sheet->insertNewRowBefore(1, 5);
                
                // Add Logo - centered
                $logoPath = public_path('images/logo-small.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('PASIG CATHOLIC COLLEGE Logo');
                    $drawing->setDescription('PASIG CATHOLIC COLLEGE Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setHeight(80);
                    $drawing->setCoordinates('F1'); // Start from center column
                    $drawing->setOffsetX(150);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet->getDelegate());
                }
                
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->mergeCells('A2:' . $highestColumn . '2');
                $sheet->mergeCells('A3:' . $highestColumn . '3');
                $sheet->mergeCells('A4:' . $highestColumn . '4');
                $sheet->mergeCells('A5:' . $highestColumn . '5');

                // Main title (centered below logo)
                $sheet->setCellValue('A1', 'LOST ASSETS REPORT');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '800000'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Institution name (centered below title)
                $sheet->setCellValue('A2', 'PASIG CATHOLIC COLLEGE');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '666666'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                
                // Subtitle
                $sheet->setCellValue('A3', 'Detailed Lost Assets Records');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '666666'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Generated date
                $sheet->setCellValue('A4', 'Generated on ' . now()->format('F d, Y \a\t g:i A'));
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '999999'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);
                
                // Total records header (at the top)
                $totalRecordsTop = max(0, $highestRow - 6);
                $sheet->setCellValue('A5', 'Total Lost Asset Records: ' . $totalRecordsTop);
                $sheet->getStyle('A5')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '800000'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]
                    ]
                ]);
                $sheet->getRowDimension(5)->setRowHeight(24);

                // Header row styling (now row 6)
                $sheet->getStyle('A6:' . $highestColumn . '6')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '800000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]
                    ]
                ]);

                // Data rows styling
                $sheet->getStyle('A7:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]
                    ],
                    'font' => ['name' => 'Arial', 'size' => 10],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true]
                ]);

                // Alternating row colors
                for ($row = 7; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']]
                        ]);
                    }
                }

                // Column alignments
                $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No.
                $sheet->getStyle('B:B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Asset Code
                $sheet->getStyle('F:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Floor, Room
                $sheet->getStyle('I:I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Reported Date
                $sheet->getStyle('L:L')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status
                $sheet->getStyle('M:M')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Found Date

                // Row heights
                $sheet->getRowDimension(1)->setRowHeight(45); // Logo row
                $sheet->getRowDimension(2)->setRowHeight(45); // Logo row
                $sheet->getRowDimension(3)->setRowHeight(24); // Subtitle
                $sheet->getRowDimension(4)->setRowHeight(18); // Generated date
                $sheet->getRowDimension(5)->setRowHeight(24); // Total records (moved to top)
                $sheet->getRowDimension(6)->setRowHeight(26); // Header row
                
                // For data rows, allow auto-height
                for ($row = 7; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(-1); // Auto-height
                }
            }
        ];
    }
}
