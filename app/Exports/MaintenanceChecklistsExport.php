<?php

namespace App\Exports;

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

class MaintenanceChecklistsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents
{
    protected Collection $checklists;

    public function __construct(Collection $checklists)
    {
        $this->checklists = $checklists;
    }

    public function collection()
    {
        return $this->checklists;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Maintenance ID',
            'Room Number',
            'School Year',
            'Department',
            'Program',
            'Instructor',
            'Date Reported',
            'Date Checked',
            'Checked By',
            'GSU Staff',
            'Status',
            'Total Items',
            'OK Items',
            'For Repair',
            'For Replacement',
            'Missing Assets',
            'Completed At',
            'Notes',
        ];
    }

    public function map($checklist): array
    {
        static $rowIndex = 0;
        $rowIndex++;

        $statusSummary = $checklist->status_summary;
        $scanningProgress = $checklist->scanning_progress;

        return [
            $rowIndex,
            $checklist->maintenance_id ?? 'N/A',
            $checklist->room_number ?? 'N/A',
            $checklist->school_year ?? 'N/A',
            $checklist->department ?? 'N/A',
            $checklist->program ?? 'N/A',
            $checklist->instructor ?? 'N/A',
            optional($checklist->date_reported)?->format('M d, Y') ?? 'N/A',
            optional($checklist->date_checked)?->format('M d, Y') ?? 'N/A',
            $checklist->checked_by ?? 'N/A',
            $checklist->gsu_staff ?? 'N/A',
            ucfirst(str_replace('_', ' ', $checklist->status ?? 'N/A')),
            $statusSummary['total'] ?? 0,
            $statusSummary['ok'] ?? 0,
            $statusSummary['repair'] ?? 0,
            $statusSummary['replacement'] ?? 0,
            $scanningProgress['missing'] ?? 0,
            optional($checklist->completed_at)?->format('M d, Y g:i A') ?? 'N/A',
            $checklist->notes ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
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
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:' . $highestColumn . '1');
                $sheet->mergeCells('A2:' . $highestColumn . '2');

                $sheet->setCellValue('A1', 'MAINTENANCE CHECKLISTS REPORT');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1F2937'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                $sheet->setCellValue('A2', 'Generated on ' . now()->format('F d, Y \a\t g:i A'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 12, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Header row styling (now row 3)
                $sheet->getStyle('A3:' . $highestColumn . '3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]
                    ]
                ]);

                // Data rows styling
                $sheet->getStyle('A4:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]
                    ],
                    'font' => ['name' => 'Arial', 'size' => 10],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
                ]);

                // Wrap long text in Notes column (S)
                $sheet->getStyle('S4:S' . $highestRow)->getAlignment()->setWrapText(true);

                // Alternating row colors
                for ($row = 4; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']]
                        ]);
                    }
                }

                // Column alignments
                $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No.
                $sheet->getStyle('H:I')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Dates
                $sheet->getStyle('L:Q')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status and counts
                $sheet->getStyle('R:R')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Completed At

                // Row heights
                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(24);
                for ($row = 4; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(22);
                }
            }
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // No.
            'B' => 18,  // Maintenance ID
            'C' => 15,  // Room Number
            'D' => 15,  // School Year
            'E' => 20,  // Department
            'F' => 25,  // Program
            'G' => 25,  // Instructor
            'H' => 15,  // Date Reported
            'I' => 15,  // Date Checked
            'J' => 20,  // Checked By
            'K' => 20,  // GSU Staff
            'L' => 15,  // Status
            'M' => 12,  // Total Items
            'N' => 12,  // OK Items
            'O' => 12,  // For Repair
            'P' => 15,  // For Replacement
            'Q' => 15,  // Missing Assets
            'R' => 20,  // Completed At
            'S' => 40,  // Notes
        ];
    }
}
