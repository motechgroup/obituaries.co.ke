<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ObituarySubmissionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PublicObituaryController;
use App\Http\Controllers\CandleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ObituaryController as AdminObituaryController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use Illuminate\Support\Facades\Route;

// Public Front Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

use App\Http\Controllers\BlogController;

// Static Informational Pages
Route::view('/about', 'pages.about')->name('pages.about');
Route::view('/contact', 'pages.contact')->name('pages.contact');
Route::view('/terms', 'pages.terms')->name('pages.terms');
Route::view('/privacy', 'pages.privacy')->name('pages.privacy');

// Blog & Resource Guides
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Search & Directory & County Landing Pages
Route::get('/search', [PublicObituaryController::class, 'search'])->name('obituaries.search');
Route::get('/county/{county}', [PublicObituaryController::class, 'countyIndex'])->name('obituaries.county');
Route::get('/obituary/{slug}', [PublicObituaryController::class, 'show'])->name('obituaries.show');
Route::post('/obituary/{obituary}/candle', [CandleController::class, 'store'])->name('obituaries.candle');
Route::post('/obituary/{obituary}/report', [ReportController::class, 'store'])->name('obituaries.report');
Route::get('/sitemap.xml', [PublicObituaryController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', function () {
    return response(file_get_contents(public_path('robots.txt')), 200, ['Content-Type' => 'text/plain; charset=utf-8']);
});

// Fallback route for storage files when symlink is disabled on shared hosting
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }

    // Auto-sync file to public/storage/{path} so Apache serves future requests directly
    try {
        $publicDest = public_path('storage/' . $path);
        if (!file_exists($publicDest)) {
            @mkdir(dirname($publicDest), 0755, true);
            @copy($filePath, $publicDest);
        }
    } catch (\Throwable $e) {}

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

// Fallback Login Alias for Laravel Authentication Exception
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

// Admin Panel Authentication & Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest Auth Routes
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Password Reset Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Authenticated Admin Routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Admin & Editor Obituary Management
        Route::get('/obituaries', [AdminObituaryController::class, 'index'])->name('obituaries.index');
        Route::get('/obituaries/{obituary}', [AdminObituaryController::class, 'show'])->name('obituaries.show');
        Route::get('/obituaries/{obituary}/edit', [AdminObituaryController::class, 'edit'])->name('obituaries.edit');
        Route::put('/obituaries/{obituary}', [AdminObituaryController::class, 'update'])->name('obituaries.update');
        Route::post('/obituaries/{obituary}/verify', [AdminObituaryController::class, 'verify'])->name('obituaries.verify');
        Route::delete('/obituaries/{obituary}', [AdminObituaryController::class, 'destroy'])->name('obituaries.destroy');

        // Admin & Editor Reports Moderation
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/{report}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');
        Route::delete('/reports/{report}', [AdminReportController::class, 'destroy'])->name('reports.destroy');

        // Admin Payment Audit Logs & Finance Reports
        Route::get('/payments/export', [AdminPaymentController::class, 'export'])->name('payments.export');
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');

        // Admin Staff & Roles Management
        Route::resource('/users', AdminUserController::class)->except(['create', 'edit', 'show']);

        // Admin General Settings & Gateways
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/test-mail', [AdminSettingController::class, 'sendTestMail'])->name('settings.test-mail');
        Route::post('/settings/test-sms', [AdminSettingController::class, 'sendTestSms'])->name('settings.test-sms');

        // Admin Database Maintenance & System Code Operations
        Route::post('/database/migrate', [AdminSettingController::class, 'runMigrations'])->name('database.migrate');
        Route::post('/database/seed', [AdminSettingController::class, 'runSeeders'])->name('database.seed');
        Route::post('/database/purge', [AdminSettingController::class, 'purgeDatabase'])->name('database.purge');
        Route::post('/system/git-pull', [AdminSettingController::class, 'gitPull'])->name('system.git-pull');
        Route::post('/system/fix-storage', [AdminSettingController::class, 'fixStorage'])->name('system.fix-storage');
    });
});
