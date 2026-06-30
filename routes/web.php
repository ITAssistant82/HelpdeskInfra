<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssetBarcodeController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Public Route - Scan Barcode (tidak perlu login)
Route::get('/scan/{asset}', [AssetBarcodeController::class, 'scanView'])->name('asset.scan');

// Serve uploaded files (storage)
// Microsoft SSO Login
Route::prefix('auth')->group(function () {
    Route::get('/microsoft', [\App\Http\Controllers\Auth\MicrosoftController::class, 'redirect'])->name('microsoft.login');
    Route::get('/microsoft/callback', [\App\Http\Controllers\Auth\MicrosoftController::class, 'callback'])->name('microsoft.callback');
});



Route::get('/storage-file/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*')->name('storage.file');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');

    // Asset Barcode Routes
    Route::get('/asset/{asset}/barcode/print', [AssetBarcodeController::class, 'printBarcode'])->name('asset.barcode.print');
    Route::get('/asset/{asset}/barcode/preview', [AssetBarcodeController::class, 'previewBarcode'])->name('asset.barcode.preview');
    Route::get('/asset/{asset}/barcode/sticker', [AssetBarcodeController::class, 'stickerBarcode'])->name('asset.barcode.sticker');
    Route::get('/assets/barcode/print-all', [AssetBarcodeController::class, 'printMultipleBarcode'])->name('assets.barcode.print-all');
    Route::get('/assets/barcode/print-stickers', [AssetBarcodeController::class, 'printMultipleSticker'])->name('assets.barcode.print-stickers');
    Route::get('/assets/barcode/print-stickers-word', [AssetBarcodeController::class, 'printMultipleStickerWord'])->name('assets.barcode.print-stickers-word');
});
