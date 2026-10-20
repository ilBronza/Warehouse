<?php

namespace IlBronza\Warehouse;

use IlBronza\CRUD\Traits\IlBronzaPackages\IlBronzaServiceProviderPackagesTrait;
use IlBronza\Clients\Models\Destination;
use IlBronza\Warehouse\Models\Room\Room;
use IlBronza\Warehouse\Console\Commands\CheckFullyDeliveredContentDeliveriesCommand;
use IlBronza\Warehouse\Http\Middleware\WarehouseMiddlewareRolesPermissions;
use Illuminate\Support\ServiceProvider;

class WarehouseServiceProvider extends ServiceProvider
{
    use IlBronzaServiceProviderPackagesTrait;
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'warehouse');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'warehouse');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/warehouse.php');

        $destinationClass = config('clients.models.destination.class', Destination::class);
        if (class_exists($destinationClass)) {
            $destinationClass::resolveRelationUsing('rooms', function ($destination) {
                return $destination->hasMany(Room::getProjectClassName(), 'destination_id');
            });
        }

        $this->app['router']->aliasMiddleware('warehouse.roles', WarehouseMiddlewareRolesPermissions::class);

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/warehouse.php', 'warehouse');

        // Published configurations may predate the structures and rooms catalogs.
        $defaults = require __DIR__.'/../config/warehouse.php';
        foreach (['structure', 'roomCategory', 'roomType', 'room'] as $model) {
            $this->app['config']->set('warehouse.models.' . $model, array_replace_recursive(
                $defaults['models'][$model],
                $this->app['config']->get('warehouse.models.' . $model, [])
            ));
        }

        // Register the service the package provides.
        $this->app->singleton('warehouse', function ($app) {
            return new Warehouse;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['warehouse'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/warehouse.php' => config_path('warehouse.php'),
        ], 'warehouse.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/ilbronza'),
        ], 'warehouse.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/ilbronza'),
        ], 'warehouse.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/ilbronza'),
        ], 'warehouse.views');*/

        $this->commands([
            CheckFullyDeliveredContentDeliveriesCommand::class,
        ]);
    }
}
