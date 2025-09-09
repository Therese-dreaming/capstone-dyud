<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\DisposalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LostAssetController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\SemesterRecordController;
use App\Http\Controllers\SemesterSettingController;
use App\Http\Controllers\MaintenanceChecklistController;
use App\Http\Controllers\AssetScannerController;
use App\Http\Controllers\MaintenanceRequestController;

// Default route - redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'createLogin'])->name('login');
Route::post('/login', [AuthController::class, 'storeLogin']);
Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
Route::post('/register', [AuthController::class, 'storeRegister']);

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    // Dashboard - accessible by all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // API route for getting asset details (for modals)
    Route::get('/api/assets/{asset}', function (App\Models\Asset $asset) {
        return response()->json($asset->load(['category', 'location']));
    })->name('api.assets.show');
    // API route for getting asset by code (for QR scanner)
    Route::get('/api/assets/code/{assetCode}', function ($assetCode) {
        $asset = \App\Models\Asset::where('asset_code', $assetCode)
            ->with(['category', 'location'])
            ->firstOrFail();
        return response()->json($asset);
    })->name('api.assets.show-by-code');
    
    // Routes for admin only (removed user role)
    Route::middleware(['role:admin'])->group(function () {
        // Asset management for admin
        Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
        Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
        Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
        Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
        Route::put('/assets/{asset}/dispose', [AssetController::class, 'dispose'])->name('assets.dispose');
        Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
        // Route::get('/assets-report', [AssetController::class, 'report'])->name('assets.report');

        // QR Code routes for admin
        Route::get('/qrcode/asset/{assetCode}', [QRCodeController::class, 'generateAssetQR'])->name('qrcode.asset');
        Route::get('/qrcode/asset/{assetCode}/download', [QRCodeController::class, 'downloadAssetQR'])->name('qrcode.asset.download');

        // Categories for admin
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Locations for admin
        Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
        Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
        Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
        Route::get('/locations/{location}', [LocationController::class, 'show'])->name('locations.show');
        Route::get('/locations/{location}/edit', [LocationController::class, 'edit'])->name('locations.edit');
        Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');
        Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');

        // Maintenances removed; maintenance checklists replace them

        // Disposal routes for admin
        Route::get('/disposal-history', [DisposalController::class, 'history'])->name('disposals.history');
        Route::get('/disposal-history/export', [DisposalController::class, 'export'])->name('disposals.export');
        Route::get('/lost-assets/export', [LostAssetController::class, 'export'])->name('lost-assets.export');

        // User Management moved to Super Admin only

        // Lost Assets Management
        Route::get('/lost-assets', [LostAssetController::class, 'index'])->name('lost-assets.index');
        Route::get('/assets/{asset}/lost', [LostAssetController::class, 'create'])->name('lost-assets.create');
        Route::post('/assets/{asset}/lost', [LostAssetController::class, 'store'])->name('lost-assets.store');
        Route::get('/lost-assets/{lostAsset}', [LostAssetController::class, 'show'])->name('lost-assets.show');
        Route::put('/lost-assets/{lostAsset}/status', [LostAssetController::class, 'updateStatus'])->name('lost-assets.update-status');
        Route::delete('/lost-assets/{lostAsset}', [LostAssetController::class, 'destroy'])->name('lost-assets.destroy');

        // Date Range View
        Route::get('/locations/{location}/date-range', [LocationController::class, 'dateRangeView'])->name('locations.date-range');

    });

    // Maintenance Checklists - accessible to both admin and GSU users (role checking handled in controller)
    Route::middleware(['auth'])->group(function () {
        // Unverified Assets Management (Admin only) - Must be before parameterized routes
        Route::get('/maintenance-checklists/unverified-assets', [MaintenanceChecklistController::class, 'unverifiedAssets'])->name('maintenance-checklists.unverified-assets');
        Route::post('/assets/{asset}/confirm-lost', [MaintenanceChecklistController::class, 'confirmAsLost'])->name('assets.confirm-lost');
        Route::post('/assets/{asset}/mark-found', [MaintenanceChecklistController::class, 'markAsFound'])->name('assets.mark-found');
        Route::post('/assets/{asset}/resolve-repair', [MaintenanceChecklistController::class, 'resolveRepair'])->name('assets.resolve-repair');
        
        Route::get('/maintenance-checklists', [MaintenanceChecklistController::class, 'index'])->name('maintenance-checklists.index');
        Route::get('/maintenance-checklists/create', [MaintenanceChecklistController::class, 'create'])->name('maintenance-checklists.create');
        Route::post('/maintenance-checklists', [MaintenanceChecklistController::class, 'store'])->name('maintenance-checklists.store');
        
        // API route for maintenance checklist items (accessible to authenticated users)
        Route::get('/maintenance-checklists/common-items', [MaintenanceChecklistController::class, 'getCommonItems'])->name('maintenance-checklists.common-items');
        
        Route::get('/maintenance-checklists/{maintenanceChecklist}/batch-update', [MaintenanceChecklistController::class, 'batchUpdateView'])->name('maintenance-checklists.batch-update-view');
        Route::put('/maintenance-checklists/{maintenanceChecklist}/batch-update', [MaintenanceChecklistController::class, 'batchUpdate'])->name('maintenance-checklists.batch-update');
        
        Route::get('/maintenance-checklists/{maintenanceChecklist}', [MaintenanceChecklistController::class, 'show'])->name('maintenance-checklists.show');
        Route::get('/maintenance-checklists/{maintenanceChecklist}/edit', [MaintenanceChecklistController::class, 'edit'])->name('maintenance-checklists.edit');
        Route::put('/maintenance-checklists/{maintenanceChecklist}', [MaintenanceChecklistController::class, 'update'])->name('maintenance-checklists.update');
        Route::delete('/maintenance-checklists/{maintenanceChecklist}', [MaintenanceChecklistController::class, 'destroy'])->name('maintenance-checklists.destroy');
        Route::get('/maintenance-checklists/{maintenanceChecklist}/export', [MaintenanceChecklistController::class, 'exportCsv'])->name('maintenance-checklists.export');
        
        // New workflow routes
        Route::post('/maintenance-checklists/{maintenanceChecklist}/acknowledge', [MaintenanceChecklistController::class, 'acknowledge'])->name('maintenance-checklists.acknowledge');
        Route::post('/maintenance-checklists/{maintenanceChecklist}/start', [MaintenanceChecklistController::class, 'startMaintenance'])->name('maintenance-checklists.start');
        Route::get('/maintenance-checklists/{maintenanceChecklist}/scanner', [MaintenanceChecklistController::class, 'scanner'])->name('maintenance-checklists.scanner');
        Route::post('/maintenance-checklists/{maintenanceChecklist}/submit', [MaintenanceChecklistController::class, 'submitMaintenance'])->name('maintenance-checklists.submit');
        Route::post('/maintenance-checklists/{maintenanceChecklist}/complete-with-missing', [MaintenanceChecklistController::class, 'completeWithMissing'])->name('maintenance-checklists.complete-with-missing');
        
        // Asset Scanner API routes
        Route::post('/asset-scanner/scan', [AssetScannerController::class, 'scan'])->name('asset-scanner.scan');
        Route::post('/asset-scanner/mark-missing', [AssetScannerController::class, 'markMissing'])->name('asset-scanner.mark-missing');
        Route::get('/asset-scanner/{maintenanceChecklist}/progress', [AssetScannerController::class, 'getProgress'])->name('asset-scanner.progress');
    });

    // Maintenance Requests
    Route::get('/maintenance-requests/create', [MaintenanceRequestController::class, 'create'])->name('maintenance-requests.create');
    Route::post('/maintenance-requests', [MaintenanceRequestController::class, 'store'])->name('maintenance-requests.store');
    Route::get('/maintenance-requests', [MaintenanceRequestController::class, 'userIndex'])->name('maintenance-requests.user-index');
    Route::get('/maintenance-requests/{maintenanceRequest}', [MaintenanceRequestController::class, 'userShow'])->name('maintenance-requests.user-show');
    Route::get('/maintenance-checklists/{maintenanceChecklist}/user-view', [MaintenanceChecklistController::class, 'userShow'])->name('maintenance-checklists.user-show');
    Route::get('/admin/maintenance-requests', [MaintenanceRequestController::class, 'index'])->name('maintenance-requests.index');
    Route::get('/admin/maintenance-requests/{maintenanceRequest}', [MaintenanceRequestController::class, 'show'])->name('maintenance-requests.show');
    Route::post('/admin/maintenance-requests/{maintenanceRequest}/approve', [MaintenanceRequestController::class, 'approve'])->name('maintenance-requests.approve');
    Route::post('/admin/maintenance-requests/{maintenanceRequest}/reject', [MaintenanceRequestController::class, 'reject'])->name('maintenance-requests.reject');
    Route::get('/gsu/maintenance-requests/{maintenanceRequest}/acknowledge', [MaintenanceRequestController::class, 'acknowledge'])->name('maintenance-requests.acknowledge');
    
    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/api/notifications/recent', [App\Http\Controllers\NotificationController::class, 'getRecent'])->name('notifications.recent');
    Route::post('/api/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/api/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Routes for Super Admin only (User Management ONLY)
    Route::middleware(['role:superadmin'])->group(function () {
        // User Management (Super Admin ONLY - no other access)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users/create', [UserController::class, 'store'])->name('users.create');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}/delete', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Routes for GSU users only (super admin)
    Route::middleware(['role:gsu'])->group(function () {
        // GSU Asset Management (full CRUD)
        Route::get('/gsu/assets', [AssetController::class, 'gsuIndex'])->name('gsu.assets.index');
        Route::get('/gsu/assets/create', [AssetController::class, 'create'])->name('gsu.assets.create');
        Route::post('/gsu/assets', [AssetController::class, 'store'])->name('gsu.assets.store');
        Route::get('/gsu/assets/{asset}', [AssetController::class, 'show'])->name('gsu.assets.show');
        Route::get('/gsu/assets/code/{assetCode}', [AssetController::class, 'gsuShowByCode'])->name('gsu.assets.show-by-code');
        Route::get('/gsu/assets/{asset}/edit', [AssetController::class, 'edit'])->name('gsu.assets.edit');
        Route::put('/gsu/assets/{asset}', [AssetController::class, 'update'])->name('gsu.assets.update');
        Route::put('/gsu/assets/{asset}/dispose', [AssetController::class, 'dispose'])->name('gsu.assets.dispose');
        Route::delete('/gsu/assets/{asset}', [AssetController::class, 'destroy'])->name('gsu.assets.destroy');
        
        // GSU Location Management (view only - no create/edit/delete)
        Route::get('/gsu/locations', [LocationController::class, 'index'])->name('gsu.locations.index');
        Route::get('/gsu/locations/{location}', [LocationController::class, 'show'])->name('gsu.locations.show');
        
        // GSU Maintenances removed; maintenance checklists replace them
        
        // GSU QR Scanner
        Route::get('/gsu/qr-scanner', [QRCodeController::class, 'gsuScanner'])->name('gsu.qr.scanner');
        Route::get('/gsu/qrcode/asset/{assetCode}', [QRCodeController::class, 'generateAssetQR'])->name('gsu.qrcode.asset');
        Route::get('/gsu/qrcode/asset/{assetCode}/download', [QRCodeController::class, 'downloadAssetQR'])->name('gsu.qrcode.asset.download');
        
        // GSU Maintenance Checklists - routes are now shared with admin users above
        
        // GSU Asset Scanner API routes
        Route::post('/asset-scanner/scan', [AssetScannerController::class, 'scan'])->name('asset-scanner.scan');
        Route::post('/asset-scanner/mark-missing', [AssetScannerController::class, 'markMissing'])->name('asset-scanner.mark-missing');
        Route::get('/asset-scanner/{maintenanceChecklist}/progress', [AssetScannerController::class, 'getProgress'])->name('asset-scanner.progress');
        
        // GSU Reports - Only Lost Assets allowed
        Route::get('/gsu/lost-assets', [LostAssetController::class, 'index'])->name('gsu.lost-assets.index');
        
        // Debug route to test if GSU routes are working
        Route::get('/gsu/test', function() {
            return view('GSU.borrowings.test');
        })->name('gsu.test');
        
        // Debug route to check user role
        Route::get('/gsu/debug-user', function() {
            $user = auth()->user();
            return response()->json([
                'authenticated' => auth()->check(),
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? $user->name : null,
                'user_role' => $user ? $user->role : null,
                'all_attributes' => $user ? $user->getAttributes() : null,
                'role_check' => $user ? in_array($user->role, ['gsu']) : false
            ]);
        })->name('gsu.debug-user');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Public API within auth to resolve asset by asset_code
    Route::get('/api/assets/by-code/{assetCode}', function (string $assetCode) {
        $asset = App\Models\Asset::where('asset_code', $assetCode)->first();
        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }
        return response()->json($asset->load(['category', 'location']));
    })->name('api.assets.by-code');
    
    // Test route outside middleware to check if routing is working
    Route::get('/test-gsu', function() {
        return 'Test route working!';
    });
    
    // Debug route to check user role (outside middleware)
    Route::get('/debug-user-role', function() {
        $user = auth()->user();
        return response()->json([
            'authenticated' => auth()->check(),
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : null,
            'user_role' => $user ? $user->role : null,
            'all_attributes' => $user ? $user->getAttributes() : null,
            'role_check_gsu' => $user ? in_array($user->role, ['gsu']) : false,
            'role_check_admin' => $user ? in_array($user->role, ['admin']) : false,
            'role_check_superadmin' => $user ? in_array($user->role, ['superadmin']) : false
        ]);
    });
});
