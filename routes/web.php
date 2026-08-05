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
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ContributorController as AdminContributorController;
use App\Http\Controllers\Admin\SecurityLogController as AdminSecurityLogController;
use App\Http\Controllers\Admin\FraudController as AdminFraudController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdClickController;
use App\Http\Controllers\Advertiser\Auth\RegisterController as AdvertiserRegisterController;
use App\Http\Controllers\Advertiser\Auth\LoginController as AdvertiserLoginController;
use App\Http\Controllers\Advertiser\DashboardController as AdvertiserDashboardController;
use App\Http\Controllers\Advertiser\BusinessProfileController as AdvertiserBusinessProfileController;
use App\Http\Controllers\Advertiser\CampaignController as AdvertiserCampaignController;
use App\Http\Controllers\Advertiser\AnalyticsController as AdvertiserAnalyticsController;

use App\Http\Controllers\Admin\Advertising\AdminCampaignController;
use App\Http\Controllers\Admin\Advertising\AdminFinanceController;
use App\Http\Controllers\Admin\Advertising\AdminAdvertiserController;
use App\Http\Controllers\Admin\Advertising\AdminAnalyticsController;
use App\Http\Controllers\Admin\Advertising\AdminPricingController;
use App\Http\Controllers\Admin\Advertising\AdminPlacementController;
use App\Http\Controllers\Admin\Advertising\AdminCategoryController;

// Public Front Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Advertise With Us Redirect Route
Route::get('/advertise', function () {
    if (auth()->guard('advertiser')->check()) {
        return redirect()->route('advertiser.dashboard');
    }
    return redirect()->route('advertiser.login');
})->name('advertise');

// Ad Click Tracking & Redirect
Route::get('/ad/click/{campaign}', [AdClickController::class, 'redirect'])->name('ad.click');

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
Route::post('/obituary/{obituary}/candle', [CandleController::class, 'store'])->middleware('throttle:15,1')->name('obituaries.candle');
Route::post('/obituary/{obituary}/report', [ReportController::class, 'store'])->middleware('throttle:3,30')->name('obituaries.report');
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

// Obituary Submission Workflow (Protected with rate limiting: max 5 submissions/min per IP)
Route::get('/submit', [ObituarySubmissionController::class, 'create'])->name('obituaries.submit');
Route::post('/submit', [ObituarySubmissionController::class, 'store'])->middleware('throttle:5,1')->name('obituaries.store');

// Payment Workflow
Route::get('/payment/{obituary}', [PaymentController::class, 'checkout'])->name('payments.checkout');
Route::post('/payment/{obituary}/stkpush', [PaymentController::class, 'initiateStkPush'])->middleware('throttle:10,1')->name('payments.stkpush');
Route::get('/payment/{obituary}/status', [PaymentController::class, 'checkStatus'])->name('payments.status');
Route::get('/payment/{obituary}/success', [PaymentController::class, 'success'])->name('payments.success');

// Custom Secured Login Route (/access)
Route::get('/access', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/access', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('admin.login.post');

// Fallback Login Aliases for Laravel Authentication Exception & Legacy URLs
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');
Route::get('/admin/login', fn () => redirect()->route('admin.login'));

// Admin Panel Authentication & Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Password Reset Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Authenticated Admin Routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Admin Account Profile Management
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        
        // Admin & Editor Obituary Management
        Route::get('/obituaries', [AdminObituaryController::class, 'index'])->name('obituaries.index');
        Route::get('/obituaries/create', [AdminObituaryController::class, 'create'])->name('obituaries.create');
        Route::post('/obituaries', [AdminObituaryController::class, 'store'])->name('obituaries.store');
        Route::get('/obituaries/{obituary}', [AdminObituaryController::class, 'show'])->name('obituaries.show');
        Route::get('/obituaries/{obituary}/edit', [AdminObituaryController::class, 'edit'])->name('obituaries.edit');
        Route::put('/obituaries/{obituary}', [AdminObituaryController::class, 'update'])->name('obituaries.update');
        Route::post('/obituaries/{obituary}/verify', [AdminObituaryController::class, 'verify'])->name('obituaries.verify');
        Route::post('/obituaries/{obituary}/unpublish', [AdminObituaryController::class, 'unpublish'])->name('obituaries.unpublish');
        Route::delete('/obituaries/{obituary}', [AdminObituaryController::class, 'destroy'])->name('obituaries.destroy');

        // Admin & Editor Reports Moderation
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/{report}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');
        Route::delete('/reports/{report}', [AdminReportController::class, 'destroy'])->name('reports.destroy');

        // Super Admin Only Restricted Operations
        Route::middleware([\App\Http\Middleware\EnsureSuperAdmin::class])->group(function () {
            // Contributors Directory Module
            Route::get('/contributors', [AdminContributorController::class, 'index'])->name('contributors.index');
            Route::get('/contributors/export', [AdminContributorController::class, 'export'])->name('contributors.export');

            // Security Audit Logs Module
            Route::get('/security-logs', [AdminSecurityLogController::class, 'index'])->name('security-logs.index');
            Route::post('/security-logs/block-ip', [AdminSecurityLogController::class, 'blockIp'])->name('security-logs.block-ip');

            // Fraud & Threat Monitoring Module
            Route::get('/fraud-alerts', [AdminFraudController::class, 'index'])->name('fraud.index');
            Route::post('/fraud-alerts/{alert}/dismiss', [AdminFraudController::class, 'dismiss'])->name('fraud.dismiss');
            Route::post('/fraud-alerts/{alert}/block', [AdminFraudController::class, 'blockIpAndUnpublish'])->name('fraud.block');
            Route::delete('/fraud-alerts/unblock/{ip}', [AdminFraudController::class, 'unblockIp'])->name('fraud.unblock');

            // Admin Traffic Analytics & Audience Insights
            Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');

            // Admin Staff & Roles Management
            Route::resource('/users', AdminUserController::class)->except(['create', 'edit', 'show']);

            // Admin Payment Audit Logs & Finance Reports
            Route::get('/payments/export', [AdminPaymentController::class, 'export'])->name('payments.export');
            Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');

            // Admin General Settings & Gateways
            Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
            Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
            Route::post('/settings/test-mail', [AdminSettingController::class, 'sendTestMail'])->name('settings.test-mail');
            Route::post('/settings/test-sms', [AdminSettingController::class, 'sendTestSms'])->name('settings.test-sms');

            // Admin Database Maintenance & System Code Operations
            Route::post('/database/migrate', [AdminSettingController::class, 'runMigrations'])->name('database.migrate');
            Route::post('/database/seed', [AdminSettingController::class, 'runSeeders'])->name('database.seed');
            Route::post('/database/purge', [AdminSettingController::class, 'purgeDatabase'])->name('database.purge');
        });
        // Admin Advertising System Management
        Route::prefix('advertising')->name('advertising.')->group(function () {
            Route::get('/campaigns', [AdminCampaignController::class, 'index'])->name('campaigns.index');
            Route::get('/campaigns/create', [AdminCampaignController::class, 'create'])->name('campaigns.create');
            Route::post('/campaigns', [AdminCampaignController::class, 'store'])->name('campaigns.store');
            Route::get('/campaigns/{campaign}', [AdminCampaignController::class, 'show'])->name('campaigns.show');
            Route::get('/campaigns/{campaign}/edit', [AdminCampaignController::class, 'edit'])->name('campaigns.edit');
            Route::put('/campaigns/{campaign}', [AdminCampaignController::class, 'update'])->name('campaigns.update');
            Route::post('/campaigns/{campaign}/approve', [AdminCampaignController::class, 'approve'])->name('campaigns.approve');
            Route::post('/campaigns/{campaign}/reject', [AdminCampaignController::class, 'reject'])->name('campaigns.reject');
            Route::post('/campaigns/{campaign}/pause', [AdminCampaignController::class, 'pause'])->name('campaigns.pause');
            Route::post('/campaigns/{campaign}/resume', [AdminCampaignController::class, 'resume'])->name('campaigns.resume');
            Route::delete('/campaigns/{campaign}', [AdminCampaignController::class, 'destroy'])->name('campaigns.destroy');

            Route::get('/finance', [AdminFinanceController::class, 'index'])->name('finance.index');
            Route::get('/finance/export', [AdminFinanceController::class, 'exportCsv'])->name('finance.export');

            Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
            Route::get('/analytics/export', [AdminAnalyticsController::class, 'exportCsv'])->name('analytics.export');

            Route::get('/advertisers', [AdminAdvertiserController::class, 'index'])->name('advertisers.index');
            Route::get('/advertisers/{advertiser}', [AdminAdvertiserController::class, 'show'])->name('advertisers.show');
            Route::post('/advertisers/{advertiser}/toggle-status', [AdminAdvertiserController::class, 'toggleStatus'])->name('advertisers.toggle-status');

            Route::get('/pricing', [AdminPricingController::class, 'index'])->name('pricing.index');
            Route::post('/pricing', [AdminPricingController::class, 'store'])->name('pricing.store');
            Route::put('/pricing/{pricing}', [AdminPricingController::class, 'update'])->name('pricing.update');

            Route::get('/placements', [AdminPlacementController::class, 'index'])->name('placements.index');
            Route::post('/placements', [AdminPlacementController::class, 'store'])->name('placements.store');

            Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
            Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
            Route::post('/categories/{category}/toggle', [AdminCategoryController::class, 'toggleStatus'])->name('categories.toggle');
            Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');
        });

        Route::post('/system/git-pull', [AdminSettingController::class, 'gitPull'])->name('system.git-pull');
        Route::post('/system/fix-storage', [AdminSettingController::class, 'fixStorage'])->name('system.fix-storage');
    });
});

// Advertiser Portal Auth & Management Routes
Route::prefix('advertiser')->name('advertiser.')->group(function () {
    Route::get('/register', [AdvertiserRegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AdvertiserRegisterController::class, 'register'])->middleware('throttle:6,1')->name('register.post');

    Route::get('/login', [AdvertiserLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdvertiserLoginController::class, 'login'])->middleware('throttle:6,1')->name('login.post');
    Route::post('/logout', [AdvertiserLoginController::class, 'logout'])->name('logout');

    Route::middleware('auth:advertiser')->group(function () {
        Route::get('/dashboard', [AdvertiserDashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [AdvertiserBusinessProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [AdvertiserBusinessProfileController::class, 'update'])->name('profile.update');

        Route::get('/campaigns', [AdvertiserCampaignController::class, 'index'])->name('campaigns.index');
        Route::get('/campaigns/create', [AdvertiserCampaignController::class, 'create'])->name('campaigns.create');
        Route::post('/campaigns/pricing-calculator', [AdvertiserCampaignController::class, 'calculatePricing'])->name('campaigns.pricing-calculator');
        Route::post('/campaigns', [AdvertiserCampaignController::class, 'store'])->name('campaigns.store');
        Route::get('/campaigns/{campaign}/checkout', [AdvertiserCampaignController::class, 'checkout'])->name('campaigns.checkout');
        Route::post('/campaigns/{campaign}/stkpush', [AdvertiserCampaignController::class, 'initiateStkPush'])->middleware('throttle:10,1')->name('campaigns.stkpush');
        Route::get('/campaigns/{campaign}/check-status', [AdvertiserCampaignController::class, 'checkStatus'])->name('campaigns.check-status');
        Route::get('/campaigns/{campaign}', [AdvertiserCampaignController::class, 'show'])->name('campaigns.show');
        Route::get('/campaigns/{campaign}/edit', [AdvertiserCampaignController::class, 'edit'])->name('campaigns.edit');
        Route::put('/campaigns/{campaign}', [AdvertiserCampaignController::class, 'update'])->name('campaigns.update');

        Route::get('/analytics', [AdvertiserAnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/export', [AdvertiserAnalyticsController::class, 'exportCsv'])->name('analytics.export');
    });
});
