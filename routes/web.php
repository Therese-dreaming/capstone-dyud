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

Route::get('/login', [AuthController::class, 'createLogin'])->name('login');
Route::post('/login', [AuthController::class, 'storeLogin']);
Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
Route::post('/register', [AuthController::class, 'storeRegister']);

Route::get('/dashboard', [AssetController::class, 'index'])->name('dashboard');
Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
Route::put('/assets/{asset}/dispose', [AssetController::class, 'dispose'])->name('assets.dispose');
Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
Route::get('/assets-report', [AssetController::class, 'report'])->name('assets.report');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users/create', [UserController::class, 'store'])->name('users.create');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}/delete', [UserController::class, 'destroy'])->name('users.destroy');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
Route::get('/locations/{location}', [LocationController::class, 'show'])->name('locations.show');
Route::get('/locations/{location}/edit', [LocationController::class, 'edit'])->name('locations.edit');
Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update');
Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');

Route::get('/qrcode/asset/{assetCode}', [QRCodeController::class, 'generateAssetQR'])->name('qrcode.asset');
Route::get('/qrcode/asset/{assetCode}/download', [QRCodeController::class, 'downloadAssetQR'])->name('qrcode.asset.download');

// Maintenance routes
Route::get('/maintenance-history', [MaintenanceController::class, 'history'])->name('maintenances.history');
Route::get('/assets/{asset}/maintenances', [MaintenanceController::class, 'index'])->name('maintenances.index');
Route::get('/assets/{asset}/maintenances/create', [MaintenanceController::class, 'create'])->name('maintenances.create');
Route::post('/assets/{asset}/maintenances', [MaintenanceController::class, 'store'])->name('maintenances.store');
Route::get('/assets/{asset}/maintenances/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenances.show');
Route::get('/assets/{asset}/maintenances/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('maintenances.edit');
Route::put('/assets/{asset}/maintenances/{maintenance}', [MaintenanceController::class, 'update'])->name('maintenances.update');
Route::delete('/assets/{asset}/maintenances/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenances.destroy');

// Disposal routes
Route::get('/disposal-history', [DisposalController::class, 'history'])->name('disposals.history');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
