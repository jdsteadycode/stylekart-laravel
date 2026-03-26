<?php

namespace App\Providers;

// fix: correct eventserviceprovider class path
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

// class path of OrderPlacedEvent, LogOrderPlaced Listener
use App\Events\OrderPlaced;
use App\Listeners\LogOrderPlaced;
use App\Listeners\NotifyVendors;
use App\Listeners\SendOrderSuccessMail;

// class path of LowVariantStockReachedEvent, SendLowVariantStockNotification Listener
use App\Events\LowVariantStockReached;
use App\Listeners\SendLowVariantStockNotification;

class EventServiceProvider extends ServiceProvider
{

    /**
     * events and listeners to setup
     */
     protected $listen = [
        // OrderPlacedEvent would have for listeners to react upon
        OrderPlaced::class => [
            LogOrderPlaced::class,
            NotifyVendors::class,
            SendOrderSuccessMail::class,
        ],
        // LowVariantStockReachedEvent would have listeners to react upon
        LowVariantStockReached::class => [
            SendLowVariantStockNotification::class
        ],
     ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
