<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register repository bindings here
        $this->app->bind(
            'App\Interfaces\AuthRepositoryInterface',
            'App\Repositories\AuthRepository'
        );

        $this->app->bind(
            'App\Interfaces\CategoryRepositoryInterface',
            'App\Repositories\CategoryRepository'
        );

        $this->app->bind(
            'App\Interfaces\ProductRepositoryInterface',
            'App\Repositories\ProductRepository'
        );

        $this->app->bind(
            'App\Interfaces\CartRepositoryInterface',
            'App\Repositories\CartRepository'
        );

        $this->app->bind(
            'App\Interfaces\OrderRepositoryInterface',
            'App\Repositories\OrderRepository'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
