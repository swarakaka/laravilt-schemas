<?php

namespace Laravilt\Schemas;

use Illuminate\Support\ServiceProvider;

class SchemasServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravilt-schemas.php',
            'laravilt-schemas'
        );
    }

    /**
     * Boot services.
     */
    public function boot(): void
    {
        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravilt-schemas');

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravilt-schemas');

        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__.'/../config/laravilt-schemas.php' => config_path('laravilt-schemas.php'),
            ], 'laravilt-schemas-config');

            // Publish assets
            $this->publishes([
                __DIR__.'/../dist' => public_path('vendor/laravilt/schemas'),
            ], 'laravilt-schemas-assets');

            // Publish views
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravilt-schemas'),
            ], 'laravilt-schemas-views');

            // Publish translations
            $this->publishes([
                __DIR__.'/../resources/lang' => lang_path('vendor/laravilt-schemas'),
            ], 'laravilt-schemas-translations');

            // Register commands
            $this->commands([
                Commands\InstallSchemasCommand::class,
                Commands\MakeSchemaCommand::class,
            ]);
        }
    }
}
