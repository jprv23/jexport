<?php

namespace App\Jobs;

use App\Models\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;
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

        $export->progress=50;
        $export->save();

        //Ejectutar exportación con la data
        $path = storage_path("app/{$this->disk}/{$export->file_path}");

        (new FastExcel($data))->export($path);

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
