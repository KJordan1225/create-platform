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
use App\Http\Controllers\Creator\EarningsController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\PostController as PublicPostController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FanSubscriptionController;
use App\Http\Controllers\Admin\ModerationController;
use Illuminate\Http\Request;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\Creator\StripeConnectController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [ExploreController::class, 'index'])->name('explore.index');
Route::get('/creators/{slug}', [CreatorProfileController::class, 'show'])->name('creators.show');

Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

Route::get('/post/{post}', [PublicPostController::class, 'show'])->name('posts.show');

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

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/subscriptions', [FanSubscriptionController::class, 'index'])->name('subscriptions.index');

    Route::get('/creator/settings/payouts', [StripeConnectController::class, 'settings'])
        ->name('creator.settings.payouts');

    Route::get('/creator/stripe/connect', [StripeConnectController::class, 'connect'])
        ->name('creator.stripe.connect');

    Route::get('/creator/stripe/refresh', [StripeConnectController::class, 'refresh'])
        ->name('creator.stripe.refresh');

    Route::get('/creator/stripe/return', [StripeConnectController::class, 'handleReturn'])
        ->name('creator.stripe.return');

    Route::prefix('creator')
        ->name('creator.')
        ->middleware('creator')
        ->group(function () {
            Route::get('/dashboard', [CreatorDashboardController::class, 'index'])->name('dashboard');

            Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

            Route::delete('/posts/{post}/media/{media}', [PostController::class, 'destroyMedia'])->name('posts.media.destroy');
            Route::resource('posts', PostController::class);

            Route::get('/earnings', [EarningsController::class, 'index'])->name('earnings.index');
            Route::get('/creator/posts', [PostController::class, 'index'])->name('creator.posts.index');
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

            Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

            Route::post('/moderation/posts/{post}/hide', [ModerationController::class, 'hidePost'])->name('moderation.posts.hide');
            Route::post('/moderation/posts/{post}/publish', [ModerationController::class, 'publishPost'])->name('moderation.posts.publish');
            Route::delete('/moderation/posts/{post}', [ModerationController::class, 'deletePost'])->name('moderation.posts.delete');

            Route::post('/moderation/comments/{comment}/hide', [ModerationController::class, 'hideComment'])->name('moderation.comments.hide');
            Route::post('/moderation/comments/{comment}/show', [ModerationController::class, 'showComment'])->name('moderation.comments.show');
            Route::delete('/moderation/comments/{comment}', [ModerationController::class, 'deleteComment'])->name('moderation.comments.delete');
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

Route::post('/messages/start/{creator:username}', [MessageController::class, 'start'])
    ->middleware('throttle:messages')
    ->name('messages.start');

Route::post('/messages/{conversation}', [MessageController::class, 'store'])
    ->middleware('throttle:messages')
    ->name('messages.store');
	
Route::prefix('help')->name('help.')->group(function () {
    Route::get('/', [HelpController::class, 'index'])->name('index');
    Route::get('/creator-guide', [HelpController::class, 'creatorGuide'])->name('creator');
    Route::get('/fan-guide', [HelpController::class, 'fanGuide'])->name('fan');
    Route::get('/admin-operations', [HelpController::class, 'adminGuide'])->name('admin');
});


require __DIR__.'/auth.php';
