<?php

namespace Jeanp\JExport;

use Jeanp\JExport\Jobs\JExportJob;
use Jeanp\JExport\JQueue;
use App\Models\Export;

class JExport{

    public static $disk;
    public static $directory;
    public static $queue;


    public static function dispatch($name = '', $namespace, $args = [], $queue = null){

        self::init();

        $file_name= date('YmdHis') . ".xlsx";
        $file_path= self::$directory ."/{$file_name}";


        $export = new Export();
        $export->name = $name;
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

    private static function init(){
        self::$disk = config('jexport.disk');
        self::$directory = config('jexport.directory');
        self::$queue = config('jexport.queue');
    }



}
