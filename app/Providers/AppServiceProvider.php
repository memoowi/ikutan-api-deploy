<?php

namespace App\Providers;

use App\Models\User;
use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Allow the docs to be able access by public
        Gate::define('viewApiDocs', function (User|null $user = null) {
            return true;
        });
        // Only Expose the UI
        Scramble::configure()->expose(
            ui: '/',
            // document: '/docs/api.json',
        );
    }
}
