<?php

namespace Uocnv\OrchidAction;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Uocnv\OrchidAction\Commands\ActionCommand;

class OrchidActionServiceProvider extends ServiceProvider
{
    /**
     * The available command shortname.
     *
     * @var array
     */
    protected array $commands = [
        ActionCommand::class,
    ];
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'orchid-action');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'orchid-action');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('orchid-action.php'),
            ], 'orchid-action.config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/orchid-action'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/orchid-action'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/orchid-action'),
            ], 'lang');*/

            // Registering package commands.
            $this->commands($this->commands);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'orchid-action');

        // Register the main class to use with the facade
        $this->app->singleton('orchid-action', function () {
            return new OrchidAction;
        });

        $this->registerComponentAutoDiscovery();
    }

    protected function registerComponentAutoDiscovery()
    {
        // Rather than forcing users to register each individual component,
        // we will auto-detect the component's class based on its kebab-cased
        // alias. For instance: 'app-orchid-actions-example' => App\Orchid\Actions\Example

        // We will generate a manifest file so we don't have to do the lookup every time.
        $defaultManifestPath = app()->bootstrapPath('cache/orchid-action.php');

        $this->app->singleton(ActionFinder::class, function () use ($defaultManifestPath) {
            return new ActionFinder(
                new Filesystem(),
                config('orchid-action.manifest_path') ?: $defaultManifestPath,
                $this->generatePathFromNamespace(config('orchid-action.class_namespace'))
            );
        });

    }

    public static function generatePathFromNamespace($namespace): string
    {
        $name = Str::of($namespace)->finish('\\')->replaceFirst(app()->getNamespace(), '');
        return app('path').'/'.str_replace('\\', '/', $name);
    }
}
