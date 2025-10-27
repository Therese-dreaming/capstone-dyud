<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Analytics Report - {{ now()->format('M d, Y') }}</title>
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
            width: 80%;
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
        
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .metrics-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .metrics-grid td {
            width: 16.66%;
            padding: 0 4px;
            vertical-align: top;
        }
        
        .metric-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 8px;
            text-align: center;
            width: 100%;
        }
        
        .metric-value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 2px;
        }
        
        .metric-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .status-grid {
            width: 100%;
            border-collapse: collapse;
        }
        
        .status-grid td {
            width: 25%;
            padding: 0 4px;
            vertical-align: top;
        }
        
        .status-item {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 8px;
            width: 100%;
        }
        
        .status-item-inner {
            display: table;
            width: 100%;
        }
        
        .status-item-inner > div {
            display: table-cell;
        }
        
        .status-item-inner > div:first-child {
            text-align: left;
        }
        
        .status-item-inner > div:last-child {
            text-align: right;
        }
        
        .status-label {
            font-size: 9px;
            color: #4b5563;
        }
        
        .status-value {
            font-size: 16px;
            font-weight: bold;
        }
        
        .status-available { color: #10b981; }
        .status-in-use { color: #3b82f6; }
        .status-maintenance { color: #f59e0b; }
        .status-disposed { color: #ef4444; }
        
        .trend-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        
        .trend-table th {
            background: #f3f4f6;
            padding: 6px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .trend-table td {
            padding: 6px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 9px;
        }
        
        .trend-bar {
            background: #e5e7eb;
            height: 14px;
            border-radius: 3px;
            overflow: hidden;
            position: relative;
        }
        
        .trend-bar-fill {
            background: linear-gradient(90deg, #dc2626, #b91c1c);
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 5px;
            color: white;
            font-size: 8px;
            font-weight: bold;
        }
        
        .role-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        
        .role-grid td {
            width: 25%;
            padding: 0 4px;
            vertical-align: top;
        }
        
        .role-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 10px;
            text-align: center;
            width: 100%;
        }
        
        .role-value {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .role-admin { color: #3b82f6; }
        .role-user { color: #10b981; }
        .role-gsu { color: #ef4444; }
        .role-purchasing { color: #8b5cf6; }
        
        .role-label {
            font-size: 9px;
            color: #6b7280;
        }
        
        .info-box {
            margin-top: 8px;
            padding: 8px;
            background: #f9fafb;
            border-radius: 4px;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }
        
        .section {
            page-break-inside: avoid;
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
        <h1>Dashboard Analytics Report</h1>
        <div class="subtitle">Asset Management System</div>
        <div class="generated-date">Generated on: {{ now()->format('F d, Y \a\t h:i A') }}</div>
    </div>

    <!-- Section 1: Asset Status Overview -->
    <div class="section">
        <div class="section-title">1. Asset Status Overview</div>
        <table class="metrics-grid">
            <tr>
                <td>
                    <div class="metric-card">
                        <div class="metric-value">{{ number_format($totalAssets ?? 0) }}</div>
                        <div class="metric-label">Total Assets</div>
                    </div>
                </td>
                <td>
                    <div class="metric-card">
                        <div class="metric-value">{{ number_format($availableAssets ?? 0) }}</div>
                        <div class="metric-label">Available</div>
                    </div>
                </td>
                <td>
                    <div class="metric-card">
                        <div class="metric-value">{{ number_format($inUseAssets ?? 0) }}</div>
                        <div class="metric-label">In Use</div>
                    </div>
                </td>
                <td>
                    <div class="metric-card">
                        <div class="metric-value">{{ number_format($maintenanceAssets ?? 0) }}</div>
                        <div class="metric-label">Maintenance</div>
                    </div>
                </td>
                <td>
                    <div class="metric-card">
                        <div class="metric-value">{{ number_format($disposedAssets ?? 0) }}</div>
                        <div class="metric-label">Disposed</div>
                    </div>
                </td>
                <td>
                    <div class="metric-card">
                        <div class="metric-value">{{ number_format($pendingApprovals ?? 0) }}</div>
                        <div class="metric-label">Pending</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Section 2: System Overview -->
    <div class="section">
        <div class="section-title">2. System Overview</div>
        <table class="metrics-grid">
            <tr>
                <td>
                    <div class="metric-card">
                        <div class="metric-value">{{ number_format($totalMaintenanceRequests ?? 0) }}</div>
                        <div class="metric-label">Maintenance</div>
                    </div>
                </td>
                <td>
                    <div class="metric-card">
                        <div class="metric-value">{{ number_format($totalRepairRequests ?? 0) }}</div>
                        <div class="metric-label">Repairs</div>
                    </div>
                </td>
                <td>
                    <div class="metric-card">
                        <div class="metric-value">{{ number_format($totalCategories ?? 0) }}</div>
                        <div class="metric-label">Categories</div>
                    </div>
                </td>
                <td>
                    <div class="metric-card">
                        <div class="metric-value">{{ number_format($totalLocations ?? 0) }}</div>
                        <div class="metric-label">Locations</div>
                    </div>
                </td>
                <td>
                    <div class="metric-card">
                        <div class="metric-value">{{ number_format($totalUsers ?? 0) }}</div>
                        <div class="metric-label">Total Users</div>
                    </div>
                </td>
                <td>
                    <div class="metric-card">
                        <div class="metric-value">{{ round(($availableAssets / max($totalAssets, 1)) * 100) }}%</div>
                        <div class="metric-label">Availability</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Section 3: Asset Status Distribution -->
    <div class="section">
        <div class="section-title">3. Asset Status Distribution</div>
        <table class="status-grid">
            <tr>
                <td>
                    <div class="status-item">
                        <div class="status-item-inner">
                            <div><span class="status-label">Available Assets</span></div>
                            <div><span class="status-value status-available">{{ number_format($availableAssets ?? 0) }}</span></div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="status-item">
                        <div class="status-item-inner">
                            <div><span class="status-label">In Use</span></div>
                            <div><span class="status-value status-in-use">{{ number_format($inUseAssets ?? 0) }}</span></div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="status-item">
                        <div class="status-item-inner">
                            <div><span class="status-label">For Maintenance</span></div>
                            <div><span class="status-value status-maintenance">{{ number_format($maintenanceAssets ?? 0) }}</span></div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="status-item">
                        <div class="status-item-inner">
                            <div><span class="status-label">Disposed</span></div>
                            <div><span class="status-value status-disposed">{{ number_format($disposedAssets ?? 0) }}</span></div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        
        <div class="info-box">
            <div style="font-size: 9px; color: #6b7280; margin-bottom: 5px;">Asset Availability Rate</div>
            <div class="trend-bar">
                <div class="trend-bar-fill" style="width: {{ round(($availableAssets / max($totalAssets, 1)) * 100) }}%; background: #dc2626;">
                    {{ round(($availableAssets / max($totalAssets, 1)) * 100) }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Section 4: User Role Distribution -->
    <div class="section">
        <div class="section-title">4. User Role Distribution</div>
        <table class="role-grid">
            <tr>
                <td>
                    <div class="role-card">
                        <div class="role-value role-admin">{{ number_format($adminUsers ?? 0) }}</div>
                        <div class="role-label">Admin</div>
                    </div>
                </td>
                <td>
                    <div class="role-card">
                        <div class="role-value role-user">{{ number_format($regularUsers ?? 0) }}</div>
                        <div class="role-label">Users</div>
                    </div>
                </td>
                <td>
                    <div class="role-card">
                        <div class="role-value role-gsu">{{ number_format($gsuUsers ?? 0) }}</div>
                        <div class="role-label">GSU</div>
                    </div>
                </td>
                <td>
                    <div class="role-card">
                        <div class="role-value role-purchasing">{{ number_format($purchasingUsers ?? 0) }}</div>
                        <div class="role-label">Purchasing</div>
                    </div>
                </td>
            </tr>
        </table>
        
        <div class="info-box">
            <div style="font-size: 9px; font-weight: 600; color: #374151; margin-bottom: 3px;">
                Total System Users: {{ number_format($totalUsers ?? 0) }}
            </div>
            <div style="font-size: 8px; color: #6b7280;">
                Active accounts across all roles
            </div>
        </div>
    </div>

    <!-- Section 5: Asset Creation Trend (12 Months) -->
    <div class="section">
        <div class="section-title">5. Asset Creation Trend (12 Months)</div>
        <table class="trend-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Month</th>
                    <th style="width: 15%;">Assets Created</th>
                    <th style="width: 65%;">Trend</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $maxAssets = max(array_values($monthlyAssetData ?? [1]));
                @endphp
                @foreach($monthlyAssetData ?? [] as $month => $count)
                <tr>
                    <td>{{ $month }}</td>
                    <td><strong>{{ number_format($count) }}</strong></td>
                    <td>
                        <div class="trend-bar">
                            <div class="trend-bar-fill" style="width: {{ $maxAssets > 0 ? round(($count / $maxAssets) * 100) : 0 }}%; background: #dc2626;">
                                {{ $count > 0 ? $count : '' }}
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="info-box">
            <div style="font-size: 9px; color: #6b7280;">
                <strong>Total Assets Created (12 months):</strong> {{ number_format(array_sum($monthlyAssetData ?? [0])) }}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>Asset Management System - Dashboard Analytics Report</div>
        <div>This report is confidential and intended for internal use only.</div>
        <div style="margin-top: 5px;">© {{ now()->year }} All Rights Reserved</div>
    </div>
</body>
</html>
