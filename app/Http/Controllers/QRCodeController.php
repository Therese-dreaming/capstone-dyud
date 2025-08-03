<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Asset;

class QRCodeController extends Controller
{
    public function generateAssetQR($assetCode)
    {
        // Generate QR code for asset code
        $qrCode = QrCode::format('svg')
            ->size(200)
            ->generate($assetCode);
            
        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml');
    }

    public function downloadAssetQR($assetCode)
    {
        // Generate QR code as PNG for download
        $qrCode = QrCode::format('png')
            ->size(300)
            ->generate($assetCode);
            
        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="asset-' . $assetCode . '.png"');
    }

    public function gsuScanner()
    {
        // GSU-specific QR scanner view
        return view('assets.gsu-qr-scanner');
    }
}
