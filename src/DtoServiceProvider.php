<?php

namespace Bpartner\Dto;

use Bpartner\Dto\CreatorsValue\ArrayType;
use Bpartner\Dto\CreatorsValue\CarbonType;
use Bpartner\Dto\CreatorsValue\CollectionType;
use Bpartner\Dto\CreatorsValue\DefaultType;
use Bpartner\Dto\CreatorsValue\DtoType;
use Bpartner\Dto\CreatorsValue\ScalarType;
use Illuminate\Support\ServiceProvider;

class DtoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /**
         * Optional methods to load your package assets
         */
        $this->registerPropertiesType();
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('dto.php'),
            ], 'config');

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'dto');

        // Register the main class to use with the facade
        $this->app->singleton('dto', function () {
            return new DtoFactory();
        });
    }

    private function registerPropertiesType(): void
    {
        TypeResolver::register(DefaultType::class);
        TypeResolver::register(ScalarType::class);
        TypeResolver::register(CarbonType::class);
        TypeResolver::register(ArrayType::class);
        TypeResolver::register(CollectionType::class);
        TypeResolver::register(DtoType::class);
    }
}
