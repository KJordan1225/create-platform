<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\CreatorProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TipController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Creator\DashboardController as CreatorDashboardController;
use App\Http\Controllers\Creator\PostController;
use App\Http\Controllers\Creator\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [ExploreController::class, 'index'])->name('explore.index');
Route::get('/creators/{slug}', [CreatorProfileController::class, 'show'])->name('creators.show');

Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/subscribe/{creator:username}', [SubscriptionController::class, 'showCheckout'])->name('subscriptions.checkout');
    Route::post('/subscribe/{creator:username}', [SubscriptionController::class, 'checkout'])->name('subscriptions.store');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscriptions.success');
    Route::post('/subscription/{creator:username}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

    Route::get('/tip/{creator:username}', [TipController::class, 'showCheckout'])->name('tips.checkout');
    Route::post('/tip/{creator:username}', [TipController::class, 'checkout'])->name('tips.store');
    Route::get('/tip/success', [TipController::class, 'success'])->name('tips.success');

    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

    Route::prefix('creator')
        ->name('creator.')
        ->middleware('creator')
        ->group(function () {
            Route::get('/dashboard', [CreatorDashboardController::class, 'index'])->name('dashboard');

            Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

            Route::resource('posts', PostController::class);
        });
});

require __DIR__.'/auth.php';
