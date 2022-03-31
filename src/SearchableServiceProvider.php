<?php

namespace Flarone\Searchable;

use Flarone\Searchable\Console\Commands\GenerateSearchIndex;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SearchableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the package's services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadPublisers();
        $this->loadRoutes();
        $this->loadViews();
    }

    /**
     * Register the package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/searchable.php', 'searchable');
        $this->registerCommands();
    }

    /**
     * Load the package config.
     *
     * @return void
     */
    private function loadPublisers()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/searchable.php' => config_path('searchable.php'),
            ], 'config');
        }
    }

    /**
     * Load the package routes.
     *
     * @return void
     */
    private function loadRoutes()
    {
        Route::middleware('api')->prefix('api')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        });
        Route::middleware('web')->prefix(config('searchable.slug'))->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        });
    }

    /**
     * Load the package views.
     *
     * @return void
     */
    private function loadViews()
    {
        $viewsPath = $this->packagePath('resources/views');
        $this->loadViewsFrom($viewsPath, 'searchable');
    }

    /**
     * Get the absolute path to some package resource.
     *
     * @param string $path The relative path to the resource
     * @return string
     */
    private function packagePath($path)
    {
        return __DIR__."/../$path";
    }

    /**
     * Register the package's artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->commands([
            GenerateSearchIndex::class
        ]);
    }
}