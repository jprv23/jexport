<?php

namespace Jeanp\JExport;

use App\Jobs\JExportJob;
use App\Models\Export;

class JExport{

    public static $disk = 'public';
    public static $directory = 'exports';
    public static $queue = 'exports';


    public static function dispatch($namespace, $args = [], $queue = null){

        $file_name= date('YmdHis') . ".xlsx";
        $file_path= self::$directory ."/{$file_name}";


        $export = new Export();
        $export->file_name = $file_name;
        $export->file_path = $file_path;
        $export->progress = 0;
        $export->save();


        if($queue){
            self::$queue = $queue;
        }

        //Verificar si la cola está activa sino inicializar
        $jQueue = new JQueue();
        $jQueue->start([self::$queue]);

        //Agregar a la cola
        dispatch(new JExportJob($namespace, $args, $export->id, self::$disk))->onQueue(self::$queue);

        return $export;
    }



}
