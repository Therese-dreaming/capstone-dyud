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
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\UserBorrowingController;

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
    
    // User borrowing routes - accessible by all authenticated users
    Route::get('/user/borrowing', [UserBorrowingController::class, 'index'])->name('user.borrowing.index');
    Route::get('/user/borrowing/create', [UserBorrowingController::class, 'create'])->name('user.borrowing.create');
    Route::post('/user/borrowing', [UserBorrowingController::class, 'store'])->name('user.borrowing.store');
    Route::get('/user/borrowing/{borrowing}', [UserBorrowingController::class, 'show'])->name('user.borrowing.show');
    Route::get('/user/borrowing/current', [UserBorrowingController::class, 'current'])->name('user.borrowing.current');
    Route::get('/user/borrowing/overdue', [UserBorrowingController::class, 'overdue'])->name('user.borrowing.overdue');
    Route::delete('/user/borrowing/{borrowing}', [UserBorrowingController::class, 'cancel'])->name('user.borrowing.cancel');
    
    // API route for getting available assets by category (for user borrowing)
    Route::get('/user/borrowing/available-assets', [UserBorrowingController::class, 'getAvailableAssets'])->name('user.borrowing.available-assets');
    
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
        Route::get('/assets-report', [AssetController::class, 'report'])->name('assets.report');

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

        // Maintenance routes for admin
        Route::get('/maintenance-history', [MaintenanceController::class, 'history'])->name('maintenances.history');
        Route::get('/assets/{asset}/maintenances', [MaintenanceController::class, 'index'])->name('maintenances.index');
        Route::get('/assets/{asset}/maintenances/create', [MaintenanceController::class, 'create'])->name('maintenances.create');
        Route::post('/assets/{asset}/maintenances', [MaintenanceController::class, 'store'])->name('maintenances.store');
        Route::get('/assets/{asset}/maintenances/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenances.show');
        Route::get('/assets/{asset}/maintenances/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('maintenances.edit');
        Route::put('/assets/{asset}/maintenances/{maintenance}', [MaintenanceController::class, 'update'])->name('maintenances.update');
        Route::delete('/assets/{asset}/maintenances/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenances.destroy');

        // Disposal routes for admin
        Route::get('/disposal-history', [DisposalController::class, 'history'])->name('disposals.history');

        // User Management (Admin and GSU)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users/create', [UserController::class, 'store'])->name('users.create');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}/delete', [UserController::class, 'destroy'])->name('users.destroy');

        // Note: Borrowing functionality moved to user routes only
    });

    // Routes for GSU users only (super admin)
    Route::middleware(['role:gsu'])->group(function () {
        // GSU Asset Management (full CRUD)
        Route::get('/gsu/assets', [AssetController::class, 'gsuIndex'])->name('gsu.assets.index');
        Route::get('/gsu/assets/create', [AssetController::class, 'create'])->name('gsu.assets.create');
        Route::post('/gsu/assets', [AssetController::class, 'store'])->name('gsu.assets.store');
        Route::get('/gsu/assets/{asset}', [AssetController::class, 'show'])->name('gsu.assets.show');
        Route::get('/gsu/assets/{asset}/edit', [AssetController::class, 'edit'])->name('gsu.assets.edit');
        Route::put('/gsu/assets/{asset}', [AssetController::class, 'update'])->name('gsu.assets.update');
        Route::put('/gsu/assets/{asset}/dispose', [AssetController::class, 'dispose'])->name('gsu.assets.dispose');
        Route::delete('/gsu/assets/{asset}', [AssetController::class, 'destroy'])->name('gsu.assets.destroy');
        
        // GSU QR Scanner
        Route::get('/gsu/qr-scanner', [QRCodeController::class, 'gsuScanner'])->name('gsu.qr.scanner');
        Route::get('/gsu/qrcode/asset/{assetCode}', [QRCodeController::class, 'generateAssetQR'])->name('gsu.qrcode.asset');
        Route::get('/gsu/qrcode/asset/{assetCode}/download', [QRCodeController::class, 'downloadAssetQR'])->name('gsu.qrcode.asset.download');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
