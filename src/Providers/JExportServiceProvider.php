<?php

namespace Jeanp\Jexcel\Providers;

use Illuminate\Support\ServiceProvider;

class PHPExcelMacroServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MiComando::class,
            ]);
        }
    }
}
