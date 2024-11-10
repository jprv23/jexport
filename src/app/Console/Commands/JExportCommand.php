<?php

namespace App\Console\Commands;

use App\Models\Colaborador;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SubscriptionStatus;
use App\Services\SenatiService;
use App\Services\ZohoService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class JExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jexport:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para copiar migración, modelo';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $paquete = 'jexport';
        $origen = base_path("vendor/{$paquete}/database/migrations");
        $destino = database_path('migrations');

        if (!File::exists($origen)) {
            $this->error("El paquete {$paquete} no tiene una carpeta de migraciones en {$origen}");
            return;
        }

        File::copyDirectory($origen, $destino);

        $this->info("Migraciones copiadas de {$paquete} a la carpeta de migraciones del proyecto.");
    }
}
