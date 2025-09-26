<?php

namespace App\Exports;

use App\Models\MaintenanceChecklist;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MaintenanceChecklistItemsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents
{
    protected MaintenanceChecklist $checklist;

    public function __construct(MaintenanceChecklist $checklist)
    {
        $this->checklist = $checklist;
    }

    public function collection()
    {
        return $this->checklist->items()->with(['asset', 'location'])->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'Asset Code',
            'Asset Name/Particulars',
            'Category',
            'Quantity',
            'Initial Condition',
            'Final Condition',
            'Current Location',
            'Maintenance Status',
            'Scanned Date',
            'Scanned By',
            'Issues Found',
            'Missing Status',
            'Missing Reason',
            'Maintenance Notes',
        ];
    }

    public function map($item): array
    {
        static $rowIndex = 0;
        $rowIndex++;

        $location = $item->location ?? $item->asset?->location;
        $locationText = $location ? ($location->building . ' | Floor ' . $location->floor . ' • Room ' . $location->room) : ($item->location_name ?? 'N/A');

        // Determine maintenance status based on conditions
        $maintenanceStatus = 'Pending';
        if ($item->is_missing) {
            $maintenanceStatus = 'Missing';
        } elseif ($item->is_scanned) {
            $maintenanceStatus = match($item->end_status) {
                'OK' => 'Completed - Good Condition',
                'FOR REPAIR' => 'Completed - Needs Repair',
                'FOR REPLACEMENT' => 'Completed - Needs Replacement',
                default => 'Completed'
            };
        }

        // Get asset name from the related asset or use particulars
        $assetName = $item->asset?->name ?? $item->particulars ?? 'N/A';
        $category = $item->asset?->category?->name ?? 'N/A';

        return [
            $rowIndex,
            $item->asset_code ?? 'N/A',
            $assetName,
            $category,
            $item->quantity ?? 1,
            $item->start_status ?? 'N/A',
            $item->end_status ?? 'Pending',
            $locationText,
            $maintenanceStatus,
            optional($item->scanned_at)?->format('M d, Y g:i A') ?? 'Not Scanned',
            $item->scanned_by ?? 'N/A',
            $item->end_status === 'OK' ? 'None' : ($item->end_status ?? 'Pending Assessment'),
            $item->is_missing ? 'MISSING' : 'Present',
            $item->missing_reason ?? ($item->is_missing ? 'Not specified' : 'N/A'),
            $item->notes ?? 'No notes',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
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

                // Insert title and subtitle
                $sheet->insertNewRowBefore(1, 3);
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->mergeCells('A2:' . $highestColumn . '2');
                $sheet->mergeCells('A3:' . $highestColumn . '3');

                $sheet->setCellValue('A1', 'MAINTENANCE CHECKLIST ITEMS REPORT');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '059669'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                $sheet->setCellValue('A2', 'Checklist: ' . $this->checklist->maintenance_id . ' - ' . $this->checklist->room_number);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '374151']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                $sheet->setCellValue('A3', 'Generated on ' . now()->format('F d, Y \a\t g:i A'));
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Header row styling (now row 4)
                $sheet->getStyle('A4:' . $highestColumn . '4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]
                    ]
                ]);

                // Data rows styling
                $sheet->getStyle('A5:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]
                    ],
                    'font' => ['name' => 'Arial', 'size' => 10],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Wrap long text in certain columns
                $sheet->getStyle('C5:C' . $highestRow)->getAlignment()->setWrapText(true); // Particulars
                $sheet->getStyle('G5:G' . $highestRow)->getAlignment()->setWrapText(true); // Location
                $sheet->getStyle('L5:L' . $highestRow)->getAlignment()->setWrapText(true); // Missing Reason
                $sheet->getStyle('M5:M' . $highestRow)->getAlignment()->setWrapText(true); // Notes

                // Alternating row colors
                for ($row = 5; $row <= $highestRow; $row++) {
                    if ($row % 2 == 1) {
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']]
                        ]);
                    }
                }

                // Column alignments
                $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No.
                $sheet->getStyle('D:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Quantity
                $sheet->getStyle('E:F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status
                $sheet->getStyle('H:K')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Scanned/Missing
                $sheet->getStyle('I:I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Scanned At

                // Row heights
                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(18);
                $sheet->getRowDimension(4)->setRowHeight(24);
                for ($row = 5; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(22);
                }
            }
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // No.
            'B' => 25,  // Asset Code
            'C' => 30,  // Asset Name/Particulars
            'D' => 25,  // Category
            'E' => 10,  // Quantity
            'F' => 25,  // Initial Condition
            'G' => 25,  // Final Condition
            'H' => 40,  // Current Location
            'I' => 30,  // Maintenance Status
            'J' => 20,  // Scanned Date
            'K' => 25,  // Scanned By
            'L' => 30,  // Issues Found
            'M' => 15,  // Missing Status
            'N' => 35,  // Missing Reason
            'O' => 30,  // Maintenance Notes
        ];
    }
}
