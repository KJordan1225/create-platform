<?php

use App\Http\Controllers\Admin\CreatorManagementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Creator\ApplyController;
use App\Http\Controllers\Creator\DashboardController as CreatorDashboardController;
use App\Http\Controllers\Creator\PostController;
use App\Http\Controllers\Creator\ProfileController;
use App\Http\Controllers\CreatorProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\FanFeedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TipController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportManagementController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [ExploreController::class, 'index'])->name('explore.index');
Route::get('/creators/{slug}', [CreatorProfileController::class, 'show'])->name('creators.show');

Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/creator/apply', [ApplyController::class, 'create'])->name('creator.apply');
    Route::post('/creator/apply', [ApplyController::class, 'store'])->name('creator.apply.store');

    Route::get('/subscribe/{creator:username}', [SubscriptionController::class, 'showCheckout'])->name('subscriptions.checkout');
    Route::post('/subscribe/{creator:username}', [SubscriptionController::class, 'checkout'])->name('subscriptions.store');
    Route::get('/subscription/success', [SubscriptionController::class, 'success'])->name('subscriptions.success');
    Route::post('/subscription/{creator:username}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

    Route::get('/tip/{creator:username}', [TipController::class, 'showCheckout'])->name('tips.checkout');
    Route::post('/tip/{creator:username}', [TipController::class, 'checkout'])->name('tips.store');
    Route::get('/tip/success', [TipController::class, 'success'])->name('tips.success');

    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
	
	Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
	Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
	Route::post('/messages/start/{creator}', [MessageController::class, 'start'])->name('messages.start');
	Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/start/{creator:username}', [MessageController::class, 'start'])->name('messages.start');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/start/{creator:username}', [MessageController::class, 'start'])->name('messages.start');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');

    Route::get('/feed', FanFeedController::class)->name('feed.index');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read_all');

    Route::prefix('creator')
        ->name('creator.')
        ->middleware('creator')
        ->group(function () {
            Route::get('/dashboard', [CreatorDashboardController::class, 'index'])->name('dashboard');

            Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

            Route::delete('/posts/{post}/media/{media}', [PostController::class, 'destroyMedia'])->name('posts.media.destroy');
            Route::resource('posts', PostController::class);
        });

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('admin')
        ->group(function () {
            Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

            Route::get('/creators', [CreatorManagementController::class, 'index'])->name('creators.index');
            Route::get('/creators/{user}', [CreatorManagementController::class, 'show'])->name('creators.show');
            Route::post('/creators/{user}/approve', [CreatorManagementController::class, 'approve'])->name('creators.approve');
            Route::post('/creators/{user}/suspend', [CreatorManagementController::class, 'suspend'])->name('creators.suspend');
            Route::post('/creators/{user}/reactivate', [CreatorManagementController::class, 'reactivate'])->name('creators.reactivate');
			
			Route::get('/reports', [ReportManagementController::class, 'index'])->name('reports.index');
			Route::post('/reports/{report}/resolve', [ReportManagementController::class, 'resolve'])->name('reports.resolve');
			Route::post('/reports/{report}/dismiss', [ReportManagementController::class, 'dismiss'])->name('reports.dismiss');
        });
});

Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
    ->middleware('throttle:comments')
    ->name('comments.store');

Route::post('/reports', [ReportController::class, 'store'])
    ->middleware('throttle:reports')
    ->name('reports.store');
	
RateLimiter::for('payments', function (Request $request) {
    return [
        Limit::perMinute(6)->by($request->user()?->id ?: $request->ip()),
    ];
});

Route::post('/subscribe/{creator:username}', [SubscriptionController::class, 'checkout'])
    ->middleware('throttle:payments')
    ->name('subscriptions.store');

Route::post('/tip/{creator:username}', [TipController::class, 'checkout'])
    ->middleware('throttle:payments')
    ->name('tips.store');
	


require __DIR__.'/auth.php';
