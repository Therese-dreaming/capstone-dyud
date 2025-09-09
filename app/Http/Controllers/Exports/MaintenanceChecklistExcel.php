<?php

namespace App\Http\Controllers\Exports;

use App\Models\MaintenanceChecklist;

class MaintenanceChecklistExcel
{
    public static function buildExcelXml(MaintenanceChecklist $checklist): string
    {
        $checklist->load('items');
        $xmlHeader = '<?xml version="1.0"?>' .
            '<?mso-application progid="Excel.Sheet"?>';

        $workbookOpen = '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ' .
            'xmlns:o="urn:schemas-microsoft-com:office:office" ' .
            'xmlns:x="urn:schemas-microsoft-com:office:excel" ' .
            'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ' .
            'xmlns:html="http://www.w3.org/TR/REC-html40">';

        $styles = '<Styles>
            <Style ss:ID="Header"><Font ss:Bold="1"/><Alignment ss:Horizontal="Center"/></Style>
            <Style ss:ID="Bold"><Font ss:Bold="1"/></Style>
            <Style ss:ID="Wrap"><Alignment ss:WrapText="1"/></Style>
        </Styles>';

        // Checklist sheet
        $sheet1 = '<Worksheet ss:Name="Checklist"><Table>'; 
        // Title row
        $sheet1 .= '<Row><Cell ss:MergeAcross="5" ss:StyleID="Header"><Data ss:Type="String">Maintenance Checklist</Data></Cell></Row>';
        $sheet1 .= '<Row/>';
        // Header info
        $headerInfo = [
            ['School Year', $checklist->school_year],
            ['Department', $checklist->department],
            ['Date Reported', $checklist->date_reported ? $checklist->date_reported->format('Y-m-d') : ''],
            ['Program', $checklist->program ?? ''],
            ['Room Number', $checklist->room_number],
            ['Instructor', $checklist->instructor],
        ];
        foreach ($headerInfo as $row) {
            $sheet1 .= '<Row><Cell ss:StyleID="Bold"><Data ss:Type="String">' . htmlspecialchars($row[0]) . '</Data></Cell>' .
                       '<Cell><Data ss:Type="String">' . htmlspecialchars((string)$row[1]) . '</Data></Cell></Row>';
        }
        $sheet1 .= '<Row/>';
        // Table header
        $columns = ['ASSET CODE','PARTICULARS/ITEMS','QUANTITY','START OF SY STATUS','END OF SY STATUS','NOTES'];
        $sheet1 .= '<Row>';
        foreach ($columns as $col) {
            $sheet1 .= '<Cell ss:StyleID="Bold"><Data ss:Type="String">' . htmlspecialchars($col) . '</Data></Cell>';
        }
        $sheet1 .= '</Row>';
        // Items
        foreach ($checklist->items as $item) {
            $sheet1 .= '<Row>' .
                '<Cell><Data ss:Type="String">' . htmlspecialchars((string)($item->asset_code ?? '')) . '</Data></Cell>' .
                '<Cell><Data ss:Type="String">' . htmlspecialchars((string)$item->particulars) . '</Data></Cell>' .
                '<Cell><Data ss:Type="Number">' . (int)$item->quantity . '</Data></Cell>' .
                '<Cell><Data ss:Type="String">' . htmlspecialchars((string)$item->start_status) . '</Data></Cell>' .
                '<Cell><Data ss:Type="String">' . htmlspecialchars((string)($item->end_status ?? '')) . '</Data></Cell>' .
                '<Cell ss:StyleID="Wrap"><Data ss:Type="String">' . htmlspecialchars((string)($item->notes ?? '')) . '</Data></Cell>' .
            '</Row>';
        }
        $sheet1 .= '</Table></Worksheet>';

        // Summary sheet
        $totalItems = $checklist->items->count();
        $okCount = $checklist->items->where('end_status', 'OK')->count();
        $repairCount = $checklist->items->where('end_status', 'FOR REPAIR')->count();
        $replacementCount = $checklist->items->where('end_status', 'FOR REPLACEMENT')->count();
        $missingCount = $checklist->items->where('is_missing', true)->count();
        $scannedCount = $checklist->items->where('is_scanned', true)->count();

        $sheet2 = '<Worksheet ss:Name="Summary"><Table>';
        $sheet2 .= '<Row><Cell ss:MergeAcross="3" ss:StyleID="Header"><Data ss:Type="String">Summary</Data></Cell></Row>';
        $sheet2 .= '<Row/>';
        $summaryRows = [
            ['Total Items', $totalItems],
            ['OK', $okCount],
            ['For Repair', $repairCount],
            ['For Replacement', $replacementCount],
            ['Scanned', $scannedCount],
            ['Missing', $missingCount],
        ];
        foreach ($summaryRows as $row) {
            $sheet2 .= '<Row><Cell ss:StyleID="Bold"><Data ss:Type="String">' . htmlspecialchars($row[0]) . '</Data></Cell>' .
                       '<Cell><Data ss:Type="Number">' . (int)$row[1] . '</Data></Cell></Row>';
        }
        $sheet2 .= '<Row/>';
        $footer = [
            ['Checked by', $checklist->checked_by],
            ['Date Checked', $checklist->date_checked ? $checklist->date_checked->format('Y-m-d') : ''],
            ['GSU Staff', $checklist->gsu_staff],
            ['Generated At', now()->format('Y-m-d H:i:s')],
        ];
        foreach ($footer as $row) {
            $sheet2 .= '<Row><Cell ss:StyleID="Bold"><Data ss:Type="String">' . htmlspecialchars($row[0]) . '</Data></Cell>' .
                       '<Cell><Data ss:Type="String">' . htmlspecialchars((string)$row[1]) . '</Data></Cell></Row>';
        }
        $sheet2 .= '</Table></Worksheet>';

        $xml = $xmlHeader . $workbookOpen . $styles . $sheet1 . $sheet2 . '</Workbook>';
        return $xml;
    }
}



