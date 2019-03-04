<?php

namespace ZablockiBros\Mappers;

use ZablockiBros\Mappers\Commands\AdapterInterfaceMakeCommand;
use ZablockiBros\Mappers\Commands\AdapterMakeCommand;
use ZablockiBros\Mappers\Commands\MapperMakeCommand;
use Illuminate\Support\ServiceProvider;

class MapperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                AdapterInterfaceMakeCommand::class,
                AdapterMakeCommand::class,
                MapperMakeCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     */
    public function register()
    {

    }
}
