<?php

namespace Jeanp\JExport\Jobs;

use App\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Rap2hpoutre\FastExcel\FastExcel;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class JExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $namescape,
        public array $args = [],
        public int $exportId,
        public string $disk = 'public',
        public string $driver = 'fastexcel',
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ini_set('memory_limit', config('jexport.memory_limit'));

        $export = Export::find($this->exportId);
        $export->progress=20;
        $export->save();

        $data = app($this->namescape, $this->args)->query(...$this->args);

        if($data->count() == 0){
            $export->progress=100;
            $export->error_message='No hay datos por exportar';
            $export->finished= 1;
            $export->save();

            return;
        }

        $export->rows_total = $data->count();
        $export->progress=50;
        $export->save();

        //Ejectutar exportación con la data
        $path = storage_path("app/{$this->disk}/{$export->file_path}");

        //Verificar si el directorio existe, sino crearlo
        $directory = dirname($path);
        if (!File::exists($directory)) {
            // Crear el directorio si no existe
            File::makeDirectory($directory, 0755, true);
        }

        if(self::$driver == 'fastexcel'){
            (new FastExcel($data))->export($path);
        }else{
            $className = $this->namescape;
            Excel::store(new $className, $export->file_path, $this->disk);
        }

        $export->progress=100;
        $export->finished= 1;
        $export->save();
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        $export = Export::find($this->exportId);
        $export->error_message = $exception->getMessage();
        $export->finished = 1;
        $export->save();
    }
}
