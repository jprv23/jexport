<?php

namespace Jeanp\JExport\Providers;

use Illuminate\Support\ServiceProvider;
use Jeanp\JExport\app\Console\Commands\JExportCommand;
class JExportServiceProvider extends ServiceProvider
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
                JExportCommand::class,
            ]);
        }
    }
}
