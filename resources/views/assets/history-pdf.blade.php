<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset History Report - {{ $asset->asset_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            margin: 20mm 15mm;
            size: A4;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            width: 90%;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dc2626;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            margin-bottom: 8px;
            object-fit: contain;
        }
        
        .header h1 {
            font-size: 18px;
            color: #dc2626;
            margin-bottom: 3px;
        }
        
        .header .subtitle {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .generated-date {
            font-size: 9px;
            color: #888;
            font-style: italic;
        }
        
        .asset-info {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .asset-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .asset-info td {
            padding: 5px;
            font-size: 9px;
        }
        
        .asset-info td:first-child {
            font-weight: bold;
            color: #6b7280;
            width: 30%;
        }
        
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #dc2626;
            margin: 15px 0 8px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .history-table th {
            background: #f3f4f6;
            padding: 8px 6px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            color: #374151;
            border: 1px solid #e5e7eb;
        }
        
        .history-table td {
            padding: 6px;
            border: 1px solid #e5e7eb;
            font-size: 9px;
            vertical-align: top;
        }
        
        .history-table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .type-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .type-maintenance { background: #dbeafe; color: #1e40af; }
        .type-repair { background: #fef3c7; color: #92400e; }
        .type-disposal { background: #fee2e2; color: #991b1b; }
        .type-change { background: #e0e7ff; color: #3730a3; }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 600;
        }
        
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #dbeafe; color: #1e40af; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        @php
            $logoPath = public_path('images/logo-small.png');
            $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
            $logoSrc = $logoData ? 'data:image/png;base64,' . $logoData : '';
        @endphp
        @if($logoSrc)
            <img src="{{ $logoSrc }}" alt="Logo" class="logo">
        @endif
        <h1>Asset History Report</h1>
        <div class="subtitle">Complete History & Activity Log</div>
        <div class="generated-date">Generated on: {{ now()->format('F d, Y \a\t h:i A') }}</div>
    </div>

    <!-- Asset Information -->
    <div class="asset-info">
        <table>
            <tr>
                <td>Asset Name:</td>
                <td><strong>{{ $asset->name }}</strong></td>
                <td>Asset Code:</td>
                <td><strong>{{ $asset->asset_code }}</strong></td>
            </tr>
            <tr>
                <td>Category:</td>
                <td>{{ $asset->category->name ?? 'N/A' }}</td>
                <td>Status:</td>
                <td>{{ $asset->status }}</td>
            </tr>
            <tr>
                <td>Location:</td>
                <td>{{ $asset->location ? $asset->location->building . ' - ' . $asset->location->room : 'Not Assigned' }}</td>
                <td>Total Records:</td>
                <td><strong>{{ $totalRecords }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Combined History Table -->
    <div class="section-title">Complete Asset History</div>
    
    @if($totalRecords > 0)
    <table class="history-table">
        <thead>
            <tr>
                <th style="width: 10%;">Date</th>
                <th style="width: 12%;">Type</th>
                <th style="width: 35%;">Description</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 15%;">Performed By</th>
                <th style="width: 16%;">Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($history as $record)
            <tr>
                <td>{{ $record['date'] }}</td>
                <td>
                    <span class="type-badge type-{{ strtolower($record['type']) }}">
                        {{ $record['type'] }}
                    </span>
                </td>
                <td>{{ $record['description'] }}</td>
                <td>
                    @if($record['status'])
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $record['status'])) }}">
                            {{ $record['status'] }}
                        </span>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $record['performed_by'] }}</td>
                <td>{{ $record['notes'] ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        No history records found for this asset.
    </div>
    @endif

    <!-- Summary Section -->
    <div class="section-title">Summary</div>
    <div class="asset-info">
        <table>
            <tr>
                <td>Maintenance Records:</td>
                <td><strong>{{ $maintenanceCount }}</strong></td>
                <td>Repair Records:</td>
                <td><strong>{{ $repairCount }}</strong></td>
            </tr>
            <tr>
                <td>Asset Changes:</td>
                <td><strong>{{ $changeCount }}</strong></td>
                <td>Disposal Records:</td>
                <td><strong>{{ $disposalCount }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>Asset Management System - Asset History Report</div>
        <div>This report is confidential and intended for internal use only.</div>
        <div style="margin-top: 5px;">© {{ now()->year }} All Rights Reserved</div>
    </div>
</body>
</html>
