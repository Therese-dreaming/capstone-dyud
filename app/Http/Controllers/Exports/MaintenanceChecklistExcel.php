<?php

namespace App\Http\Controllers\Exports;

use App\Models\MaintenanceChecklist;

class MaintenanceChecklistExcel
{
    public static function buildExcelXml(MaintenanceChecklist $checklist): string
    {
        $checklist->load('items.asset');
        $xmlHeader = '<?xml version="1.0"?>' .
            '<?mso-application progid="Excel.Sheet"?>';

        $workbookOpen = '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ' .
            'xmlns:o="urn:schemas-microsoft-com:office:office" ' .
            'xmlns:x="urn:schemas-microsoft-com:office:excel" ' .
            'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ' .
            'xmlns:html="http://www.w3.org/TR/REC-html40">';

        $styles = '<Styles>
            <Style ss:ID="Title">
                <Font ss:Bold="1" ss:Size="16" ss:Color="#800000"/>
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            </Style>
            <Style ss:ID="Subtitle">
                <Font ss:Size="12" ss:Color="#666666"/>
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            </Style>
            <Style ss:ID="Header">
                <Font ss:Bold="1" ss:Size="11" ss:Color="#FFFFFF"/>
                <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
                <Interior ss:Color="#800000" ss:Pattern="Solid"/>
                <Borders>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CCCCCC"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CCCCCC"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CCCCCC"/>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CCCCCC"/>
                </Borders>
            </Style>
            <Style ss:ID="SubHeader">
                <Font ss:Bold="1"/>
                <Alignment ss:Vertical="Center"/>
            </Style>
            <Style ss:ID="Cell">
                <Alignment ss:Vertical="Center"/>
                <Borders>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                </Borders>
            </Style>
            <Style ss:ID="CellAlt">
                <Alignment ss:Vertical="Center"/>
                <Interior ss:Color="#F8F9FA" ss:Pattern="Solid"/>
                <Borders>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                </Borders>
            </Style>
            <Style ss:ID="CellWrap">
                <Alignment ss:Vertical="Center" ss:WrapText="1"/>
                <Borders>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                </Borders>
            </Style>
            <Style ss:ID="CellWrapAlt">
                <Alignment ss:Vertical="Center" ss:WrapText="1"/>
                <Interior ss:Color="#F8F9FA" ss:Pattern="Solid"/>
                <Borders>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                </Borders>
            </Style>
            <Style ss:ID="StatusOK">
                <Font ss:Bold="1"/>
                <Interior ss:Color="#D1FAE5" ss:Pattern="Solid"/>
                <Borders>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#10B981"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#10B981"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#10B981"/>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#10B981"/>
                </Borders>
            </Style>
            <Style ss:ID="StatusRepair">
                <Font ss:Bold="1"/>
                <Interior ss:Color="#FEF3C7" ss:Pattern="Solid"/>
                <Borders>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#F59E0B"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#F59E0B"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#F59E0B"/>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#F59E0B"/>
                </Borders>
            </Style>
            <Style ss:ID="StatusReplace">
                <Font ss:Bold="1"/>
                <Interior ss:Color="#FFEDD5" ss:Pattern="Solid"/>
                <Borders>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#FB923C"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#FB923C"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#FB923C"/>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#FB923C"/>
                </Borders>
            </Style>
            <Style ss:ID="StatusMissing">
                <Font ss:Bold="1"/>
                <Interior ss:Color="#FEE2E2" ss:Pattern="Solid"/>
                <Borders>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#EF4444"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#EF4444"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#EF4444"/>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#EF4444"/>
                </Borders>
            </Style>
            <Style ss:ID="NumberCell">
                <NumberFormat ss:Format="0"/>
                <Borders>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                </Borders>
            </Style>
            <Style ss:ID="NumberCellAlt">
                <NumberFormat ss:Format="0"/>
                <Interior ss:Color="#F8F9FA" ss:Pattern="Solid"/>
                <Borders>
                    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                </Borders>
            </Style>
        </Styles>';

        // Checklist sheet
        $sheet1 = '<Worksheet ss:Name="Checklist">';
        // Optional column widths for better readability
        $sheet1 .= '<Table>' .
            '<Column ss:Width="120"/>' . // Asset Code
            '<Column ss:Width="260"/>' . // Particulars
            '<Column ss:Width="80"/>' .  // Qty
            '<Column ss:Width="160"/>' . // Start Status
            '<Column ss:Width="160"/>' . // End Status
            '<Column ss:Width="300"/>';  // Notes

        // Title and subtitle rows
        $sheet1 .= '<Row ss:Height="28"><Cell ss:MergeAcross="5" ss:StyleID="Title"><Data ss:Type="String">MAINTENANCE CHECKLIST REPORT</Data></Cell></Row>';
        $sheet1 .= '<Row ss:Height="20"><Cell ss:MergeAcross="5" ss:StyleID="Subtitle"><Data ss:Type="String">Generated on ' . htmlspecialchars(now()->format('F d, Y \a\t g:i A')) . '</Data></Cell></Row>';
        $sheet1 .= '<Row ss:Height="6"/>';
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
            $sheet1 .= '<Row ss:Height="20">' .
                       '<Cell ss:StyleID="SubHeader"><Data ss:Type="String">' . htmlspecialchars($row[0]) . '</Data></Cell>' .
                       '<Cell ss:MergeAcross="4" ss:StyleID="Cell"><Data ss:Type="String">' . htmlspecialchars((string)$row[1]) . '</Data></Cell>' .
                       '</Row>';
        }
        $sheet1 .= '<Row ss:Height="12"/>';
        // Table header
        $columns = ['ASSET CODE','PARTICULARS/ITEMS','QUANTITY','START OF SY STATUS','END OF SY STATUS','NOTES'];
        $sheet1 .= '<Row ss:Height="24">';
        foreach ($columns as $col) {
            $sheet1 .= '<Cell ss:StyleID="Header"><Data ss:Type="String">' . htmlspecialchars($col) . '</Data></Cell>';
        }
        $sheet1 .= '</Row>';
        // Items
        foreach ($checklist->items as $idx => $item) {
            // Map status to style IDs
            $start = strtoupper((string)$item->start_status);
            $end   = strtoupper((string)($item->end_status ?? ''));

            // Derive End of SY display for UNVERIFIED items:
            // - If asset is Lost => 'LOST'
            // - If asset is Available => 'OK'
            // - If asset is For Repair => 'FOR REPAIR'
            // - If asset is For Maintenance => 'FOR MAINTENANCE'
            // Otherwise keep original
            $derivedEnd = $end;
            if ($end === 'UNVERIFIED' && $item->asset) {
                $assetStatus = $item->asset->status;
                if ($assetStatus === 'Lost') {
                    $derivedEnd = 'LOST';
                } elseif ($assetStatus === 'Available') {
                    $derivedEnd = 'OK';
                } elseif ($assetStatus === 'For Repair') {
                    $derivedEnd = 'FOR REPAIR';
                } elseif ($assetStatus === 'For Maintenance') {
                    $derivedEnd = 'FOR MAINTENANCE';
                }
            }
            $startStyle = 'Cell';
            $endStyle = 'Cell';
            if ($start === 'OK') { $startStyle = 'StatusOK'; }
            elseif ($start === 'FOR REPAIR') { $startStyle = 'StatusRepair'; }
            elseif ($start === 'FOR REPLACEMENT') { $startStyle = 'StatusReplace'; }
            elseif ($start === 'MISSING') { $startStyle = 'StatusMissing'; }

            if ($derivedEnd === 'OK') { $endStyle = 'StatusOK'; }
            elseif ($derivedEnd === 'FOR REPAIR') { $endStyle = 'StatusRepair'; }
            elseif ($derivedEnd === 'FOR REPLACEMENT') { $endStyle = 'StatusReplace'; }
            elseif ($derivedEnd === 'MISSING' || $derivedEnd === 'LOST') { $endStyle = 'StatusMissing'; }

            $alt = ($idx % 2 === 1); // alternate background starting from second row
            $cellStyle = $alt ? 'CellAlt' : 'Cell';
            $cellWrapStyle = $alt ? 'CellWrapAlt' : 'CellWrap';
            $numberCellStyle = $alt ? 'NumberCellAlt' : 'NumberCell';

            $sheet1 .= '<Row ss:Height="22">' .
                '<Cell ss:StyleID="' . $cellStyle . '"><Data ss:Type="String">' . htmlspecialchars((string)($item->asset_code ?? '')) . '</Data></Cell>' .
                '<Cell ss:StyleID="' . $cellWrapStyle . '"><Data ss:Type="String">' . htmlspecialchars((string)$item->particulars) . '</Data></Cell>' .
                '<Cell ss:StyleID="' . $numberCellStyle . '"><Data ss:Type="Number">' . (int)$item->quantity . '</Data></Cell>' .
                '<Cell ss:StyleID="' . $startStyle . '"><Data ss:Type="String">' . htmlspecialchars((string)$item->start_status) . '</Data></Cell>' .
                '<Cell ss:StyleID="' . $endStyle . '"><Data ss:Type="String">' . htmlspecialchars((string)($derivedEnd ?? '')) . '</Data></Cell>' .
                '<Cell ss:StyleID="' . $cellWrapStyle . '"><Data ss:Type="String">' . htmlspecialchars((string)($item->notes ?? '')) . '</Data></Cell>' .
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

        $sheet2 = '<Worksheet ss:Name="Summary"><Table>' .
            '<Column ss:Width="220"/>' . // Label column wider
            '<Column ss:Width="160"/>' . // Value column wider
            '<Column ss:Width="140"/>' . // Extra columns (for merged title)
            '<Column ss:Width="140"/>';
        $sheet2 .= '<Row ss:Height="26"><Cell ss:MergeAcross="3" ss:StyleID="Title"><Data ss:Type="String">SUMMARY</Data></Cell></Row>';
        $sheet2 .= '<Row ss:Height="20"><Cell ss:MergeAcross="3" ss:StyleID="Subtitle"><Data ss:Type="String">Generated on ' . htmlspecialchars(now()->format('F d, Y \a\t g:i A')) . '</Data></Cell></Row>';
        $sheet2 .= '<Row ss:Height="8"/>';
        $summaryRows = [
            ['Total Items', $totalItems],
            ['OK', $okCount],
            ['For Repair', $repairCount],
            ['For Replacement', $replacementCount],
            ['Scanned', $scannedCount],
            ['Missing', $missingCount],
        ];
        foreach ($summaryRows as $row) {
            $sheet2 .= '<Row ss:Height="20">' .
                       '<Cell ss:StyleID="SubHeader"><Data ss:Type="String">' . htmlspecialchars($row[0]) . '</Data></Cell>' .
                       '<Cell ss:StyleID="NumberCell"><Data ss:Type="Number">' . (int)$row[1] . '</Data></Cell>' .
                       '</Row>';
        }
        $sheet2 .= '<Row ss:Height="12"/>';
        $footer = [
            ['Checked by', $checklist->checked_by],
            ['Date Checked', $checklist->date_checked ? $checklist->date_checked->format('Y-m-d') : ''],
            ['GSU Staff', $checklist->gsu_staff],
            ['Generated At', now()->format('Y-m-d H:i:s')],
        ];
        foreach ($footer as $row) {
            $sheet2 .= '<Row ss:Height="20">' .
                       '<Cell ss:StyleID="SubHeader"><Data ss:Type="String">' . htmlspecialchars($row[0]) . '</Data></Cell>' .
                       '<Cell ss:StyleID="Cell"><Data ss:Type="String">' . htmlspecialchars((string)$row[1]) . '</Data></Cell>' .
                       '</Row>';
        }
        $sheet2 .= '</Table></Worksheet>';

        $xml = $xmlHeader . $workbookOpen . $styles . $sheet1 . $sheet2 . '</Workbook>';
        return $xml;
    }
}




