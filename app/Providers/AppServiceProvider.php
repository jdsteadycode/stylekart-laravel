<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
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

        // for every blade view..
        View::composer('*', function ($view) {
            // load the main categories for header section
            $view->with('menuCategories', Category::limit(3)->get());
            $view->with('wishlistItemsCount', auth()->user()?->wishlist()?->count() ?? null);
        });
    }
}
