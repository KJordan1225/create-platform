<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use App\Models\Post;
use App\Policies\PostPolicy;
use Illuminate\Support\Facades\Gate;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        RateLimiter::for('comments', function (Request $request) {
            return [
                Limit::perMinute(10)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        RateLimiter::for('reports', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        Gate::policy(Post::class, PostPolicy::class);
    }
}
