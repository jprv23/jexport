<?php

namespace Jeanp\JExport;

use Jeanp\JExport\Jobs\JExportJob;
use Jeanp\JExport\JQueue;
use App\Models\Export;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use Jeanp\JExport\app\Http\Controllers\JExportController;

class JExport
{

    public static $disk;
    public static $directory;
    public static $queue;
    public static $userId;
    public static $driver = 'laravelexcel';

    private static function init()
    {
        self::$disk = config('jexport.disk');
        self::$directory = config('jexport.directory');
        self::$queue = config('jexport.queue');
    }

    public static function setUserId($id)
    {
        self::$userId = $id;
        return new self;
    }

    /**
     * @param driver laravelexcel|fastexcel
     */
    public static function setDriver($driver = "laravelexcel")
    {
        self::$driver = $driver;
        return new self;
    }

    public static function dispatch($name = '', $namespace, $args = [], $queue = null)
    {

        self::init();

        $file_name = self::getFilenameSanitaze($name) . "_" . date('YmdHis') . ".xlsx";
        $file_path = self::$directory . "/{$file_name}";


        $export = new Export();
        $export->name = $name;
        $export->file_name = $file_name;
        $export->file_path = $file_path;
        $export->progress = 0;

        if (self::$userId) {
            $export->user_id = self::$userId;
        }

        $export->save();


        if ($queue) {
            self::$queue = $queue;
        }

        //Verificar si la cola está activa sino inicializar
        $jQueue = new JQueue();
        $jQueue->start([self::$queue]);

        //Agregar a la cola
        dispatch(new JExportJob($namespace, $args, $export->id, self::$disk, self::$driver))->onQueue(self::$queue);

        return $export;
    }

    public static function html($namespace, $args = [])
    {
        $args['data'] = app($namespace, $args)->query(...$args);

        Excel::store(new $namespace(...$args), 'temp-html-export.html', 'local', \Maatwebsite\Excel\Excel::HTML);

        $html = file_get_contents(storage_path() . "/app/temp-html-export.html");

        unlink(storage_path('app/temp-html-export.html'));

        $temp = explode('<table', $html);
        $temp = "<table" . $temp[1];
        $temp = explode('</table>', $temp);

        $html = $temp[0] . "<thead><tr></tr></thead></table>";

        return str_replace('class="column0', 'style="width:13px" width="13px" class="column0', $html);
    }

    public static function routes()
    {
        Route::prefix('jexport')->as('jexport.')->controller(JExportController::class)->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('flush', 'flush')->name('flush');
            Route::delete('{id}', 'destroy')->name('destroy');
        });
    }

    public static function redirect($url = null)
    {

        if (!$url) {
            $url = route('jexport.index');
        }

        return redirect($url);
    }

    private static function getFilenameSanitaze($name)
    {
        $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
        $name = preg_replace('/[^a-zA-Z0-9-_.]/', '_', $name);
        $name = strtolower($name);
        $name = substr($name, 0, 200);

        return $name;
    }
}
