<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ObituarySubmissionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PublicObituaryController;
use App\Http\Controllers\CandleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ObituaryController as AdminObituaryController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use Illuminate\Support\Facades\Route;

// Public Front Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Static Informational Pages
Route::view('/about', 'pages.about')->name('pages.about');
Route::view('/contact', 'pages.contact')->name('pages.contact');
Route::view('/terms', 'pages.terms')->name('pages.terms');
Route::view('/privacy', 'pages.privacy')->name('pages.privacy');

// Search & Directory
Route::get('/search', [PublicObituaryController::class, 'search'])->name('obituaries.search');
Route::get('/obituary/{slug}', [PublicObituaryController::class, 'show'])->name('obituaries.show');
Route::post('/obituary/{obituary}/candle', [CandleController::class, 'store'])->name('obituaries.candle');
Route::get('/sitemap.xml', [PublicObituaryController::class, 'sitemap'])->name('sitemap');

// Fallback route for storage files when symlink is disabled on shared hosting
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    $mime = mime_content_type($filePath) ?: 'application/octet-stream';
    return response()->file($filePath, ['Content-Type' => $mime]);
})->where('path', '.*')->name('storage.fallback');

// Obituary Submission Workflow
Route::get('/submit', [ObituarySubmissionController::class, 'create'])->name('obituaries.submit');
Route::post('/submit', [ObituarySubmissionController::class, 'store'])->name('obituaries.store');

// Payment Workflow
Route::get('/payment/{obituary}', [PaymentController::class, 'checkout'])->name('payments.checkout');
Route::post('/payment/{obituary}/stkpush', [PaymentController::class, 'initiateStkPush'])->name('payments.stkpush');
Route::get('/payment/{obituary}/status', [PaymentController::class, 'checkStatus'])->name('payments.status');
Route::get('/payment/{obituary}/success', [PaymentController::class, 'success'])->name('payments.success');

// Admin Panel Authentication & Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Admin Obituary Management
        Route::get('/obituaries', [AdminObituaryController::class, 'index'])->name('obituaries.index');
        Route::get('/obituaries/{obituary}', [AdminObituaryController::class, 'show'])->name('obituaries.show');
        Route::get('/obituaries/{obituary}/edit', [AdminObituaryController::class, 'edit'])->name('obituaries.edit');
        Route::put('/obituaries/{obituary}', [AdminObituaryController::class, 'update'])->name('obituaries.update');
        Route::post('/obituaries/{obituary}/verify', [AdminObituaryController::class, 'verify'])->name('obituaries.verify');
        Route::delete('/obituaries/{obituary}', [AdminObituaryController::class, 'destroy'])->name('obituaries.destroy');

        // Admin Payment Audit Logs
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');

        // Admin General Settings
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});
