<?php

namespace Jeanp\JExport\app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
class JExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:jexport {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un archivo base para exportación';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $ruta = app_path('Exports');

        // Asegúrate de que la carpeta de destino existe
        if (!File::exists($ruta)) {
            File::makeDirectory($ruta, 0755, true);
        }

        // Crear la ruta completa del archivo
        $archivo = "{$ruta}/{$name}.php";

        // Verificar si el archivo ya existe
        if (File::exists($archivo)) {
            $this->error("El archivo {$archivo} ya existe.");
            return;
        }

        // Generar el contenido de la clase
        $contenido = "<?php\n\nnamespace " . $this->obtenerNamespace($ruta) . ";\n\n";
        $contenido .= "class {$name}\n{\n";
        $contenido .= "    public function query("."$"."start_date, "."$"."end_date)\n    {\n";
        $contenido .= "        // Realizar la consulta deseada\n";
        $contenido .= "        "."$"."data = [];\n\n";
        $contenido .= "        return "."$"."data;//Retornar la colección de datos\n";
        $contenido .= "    }\n";
        $contenido .= "}\n";

        // Crear el archivo con el contenido
        File::put($archivo, $contenido);

        $this->info("La clase {$name} se ha creado exitosamente en {$archivo}.");
    }

    private function obtenerNamespace($ruta)
    {
        // Convertir la ruta a namespace (por ejemplo, "app/Models" => "App\Models")
        $namespace = str_replace(base_path(), '', $ruta);
        $namespace = trim(str_replace('/', '\\', $namespace), '\\');
        $namespace = ucfirst($namespace); // Capitalizar la primera letra
        return $namespace;
    }
}
