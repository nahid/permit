<?php

namespace Nahid\Permit;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class PermitServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->setupConfig();
        $this->setupMigrations();
        $blade = new Blades();
        $blade->runCompiles();
    }
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->registerPermit();
        $this->commands([
            \Nahid\Permit\Commands\CreateRoleCommand::class,
            \Nahid\Permit\Commands\PermissionSyncCommand::class,
            \Nahid\Permit\Commands\SetPermissionCommand::class,
            \Nahid\Permit\Commands\FetchPermissionsCommand::class,
            \Nahid\Permit\Commands\RemovePermissionCommand::class,
        ]);

        $this->app->routeMiddleware(['permit' => \Nahid\Permit\Middleware\PermitMiddleware::class]);
    }
    /**
     * Setup the config.
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../config/permit.php');
        // Check if the application is a Laravel OR Lumen instance to properly merge the configuration file.
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('permit.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('permit');
        }
        $this->mergeConfigFrom($source, 'permit');
    }
    /**
     * Publish migrations files.
     */
    protected function setupMigrations()
    {
        $this->publishes([
            realpath(__DIR__.'/../database/migrations/') => database_path('migrations'),
        ], 'migrations');
    }
    /**
     * Register Talk class.
     */
    protected function registerPermit()
    {
        $this->app->singleton('permit', function (Container $app) {
            return new Permission(
                $app['config'],
                $app['Nahid\Permit\Permissions\PermissionRepository'],
                $app['Nahid\Permit\Users\UserRepository']
            );
        });

        $this->app->alias('permit', Permission::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'permit',
        ];
    }
}
