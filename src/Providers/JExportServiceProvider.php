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

        $this->publishes([
            __DIR__.'/../config/jexport.php' => config_path('jexport.php'),
            __DIR__.'/../database/migrations/create_exports_table.php' => database_path('migrations/'.date('Y_m_d_His') . '_create_exports_table.php'),
            __DIR__.'/../app/Models/Export.php' => app_path('Models/Export.php'),
            __DIR__.'/../resources/views/index.blade.php' => resource_path('jexport/index.blade.php'),
        ], 'jexport');



        if ($this->app->runningInConsole()) {
            $this->commands([
                JExportCommand::class,
            ]);
        }
    }
}
