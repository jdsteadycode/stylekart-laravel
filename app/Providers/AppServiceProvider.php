<?php

// package path
namespace App\Providers;

// Models class path
use App\Models\Category;
use App\Models\OrderItem;

// Observer class path.
use App\Observers\OrderItemObserver;

// Service provider class path
use Illuminate\Support\ServiceProvider;

// Facade(s) class path
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

// UDC - AppServiceProvider inherits ServiceProvider class
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
        // log the action.
        // logger()->info("[app\Providers\AppServiceProvider@boot] Loading of Services initiated.");

        // for every blade view..
        View::composer('*', function ($view) {
            // load the main categories for header section
            $view->with('menuCategories', Category::limit(3)->get());
            $view->with('wishlistItemsCount', auth()->user()?->wishlist()?->count() ?? null);
        });

        // Setup the OrderItem observer.
        OrderItem::observe(OrderItemObserver::class);
    }
}
