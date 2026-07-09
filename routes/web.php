<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\FormController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/products', [PageController::class, 'products'])->name('products');
Route::get('/pricing', [PageController::class, 'pricing'])->name('pricing');
Route::get('/buy', [PageController::class, 'buy'])->name('buy');
Route::get('/pro', [PageController::class, 'pro'])->name('pro');
Route::get('/pro-spatial', [PageController::class, 'proSpatial'])->name('pro-spatial');
Route::get('/solutions', [PageController::class, 'solutions'])->name('solutions');
Route::get('/resources', [PageController::class, 'resources'])->name('resources');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/quote', [PageController::class, 'quote'])->name('quote');
Route::get('/blog', [PageController::class, 'blog'])->name('blog');
Route::get('/blog/{post}', [PageController::class, 'blogPost'])->name('blog.post');
Route::get('/page/{page}', [PageController::class, 'dynamicPage'])->name('page.show');
Route::get('/form/{form}', [FormController::class, 'show'])->name('form.show');
Route::post('/form/{form}', [FormController::class, 'submit'])->name('form.submit');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/eula', [PageController::class, 'eula'])->name('eula');
Route::get('/cookies', [PageController::class, 'cookies'])->name('cookies');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');

// Auth
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/signup', [RegisterController::class, 'show'])->name('register');
Route::post('/signup', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Social auth
Route::get('/auth/{provider}', [SocialController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialController::class, 'callback']);

// Forgot password
Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.forgot');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendPassword']);

// API
Route::get('/api/pricing', [PricingController::class, 'index'])->name('api.pricing');
Route::post('/api/quote', [QuoteController::class, 'store'])->name('api.quote');

// Dashboard (authenticated)
Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/subscriptions', [DashboardController::class, 'subscriptions'])->name('dashboard.subscriptions');
    Route::get('/subscriptions/{subscription}', [DashboardController::class, 'subscriptionDetail'])->name('dashboard.subscription.detail');
    Route::get('/subscriptions/{subscription}/print', [DashboardController::class, 'subscriptionPrint'])->name('dashboard.subscription.print');
    Route::post('/subscriptions/{subscription}/cancel', [DashboardController::class, 'cancelSubscription'])->name('dashboard.subscription.cancel');
    Route::post('/subscriptions/{subscription}/retry-invoice', [DashboardController::class, 'retryInvoicePayment'])->name('dashboard.subscription.retry-invoice');
    Route::get('/licenses', [DashboardController::class, 'licenses'])->name('dashboard.licenses');
    Route::get('/orders', [DashboardController::class, 'orders'])->name('dashboard.orders');
    Route::get('/orders/{order}', [DashboardController::class, 'orderDetail'])->name('dashboard.order.detail');
    Route::get('/orders/{order}/print', [DashboardController::class, 'orderPrint'])->name('dashboard.order.print');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::post('/profile/password', [DashboardController::class, 'updatePassword'])->name('dashboard.password.update');
});

// Razorpay
Route::middleware('auth')->prefix('api/razorpay')->group(function () {
    Route::post('/create-order', [RazorpayController::class, 'createOrder'])->name('razorpay.create-order');
    Route::post('/verify', [RazorpayController::class, 'verifyPayment'])->name('razorpay.verify');
    Route::post('/create-subscription', [RazorpayController::class, 'createSubscription'])->name('razorpay.create-subscription');
    Route::post('/create-plan', [RazorpayController::class, 'createPlanAndSubscription'])->name('razorpay.create-plan');
    Route::post('/verify-subscription', [RazorpayController::class, 'verifySubscription'])->name('razorpay.verify-subscription');
    Route::post('/cancel-pending-subscription', [RazorpayController::class, 'cancelPendingSubscription'])->name('razorpay.cancel-pending-subscription');
    Route::post('/cancel-pending-order', [RazorpayController::class, 'cancelPendingOrder'])->name('razorpay.cancel-pending-order');
});

// Razorpay webhook (no auth, no CSRF)
Route::post('/webhook/razorpay', [RazorpayController::class, 'webhook'])->name('razorpay.webhook');

// Filament admin
Route::middleware(['auth', 'verified'])->prefix('admin')->name('filament.')->group(function () {
    // Filament admin routes will be registered by the Filament package
});

// Admin invoice print (outside Filament middleware)
Route::middleware(['auth', 'verified'])->get('/admin/invoices/{order}/print', [PageController::class, 'adminInvoicePrint'])->name('admin.invoice.print');

// Handle GET /admin/logout gracefully (Filament only registers POST)
Route::get('/admin/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/admin/login');
})->name('admin.logout.get');

// CMS pages at root: /{slug} — MUST be last (catch-all)
Route::get('/{slug}', [PageController::class, 'cmsPage'])
    ->where('slug', '.*')
    ->name('cms.show');