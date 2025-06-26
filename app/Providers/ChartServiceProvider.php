<?php

namespace App\Providers;

use App\Models\Entities\TestPyDbCon;
use App\Repositories\TestPyDbConRepository;
use App\Services\ChartDataService;
use Illuminate\Support\ServiceProvider;

class ChartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the Repository
        $this->app->bind(TestPyDbConRepository::class, function ($app) {
            return new TestPyDbConRepository(new TestPyDbCon());
        });
        
        // Register the Service
        $this->app->bind(ChartDataService::class, function ($app) {
            return new ChartDataService($app->make(TestPyDbConRepository::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 