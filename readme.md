
# JExport

Biblioteca para exportar datos en segundo plano usando queues de laravel


## Instalación
#### Paso 1: Dependencia
Instalar jeanp/jexport usando composer

```bash
  composer require jeanp/jexport
```
#### Paso 2: Proveedor
Necesitas actualizar la configuración de tu aplicación para poder registrar el paquete y Laravel pueda cargarlo, simplemente actualiza tu archivo config/app.php agregando el siguiente código al final de tu sección 'providers':

```bash
<?php
return [
    // ...
    'providers' => [
        Jeanp\JExport\Providers\JExportServiceProvider::class,
        // ...
    ],
    // ...
];
```
#### Paso 3: Publicar configuración
En tu terminal escribe:

```bash
php artisan vendor:publish --provider="Jeanp\JExport\Providers\JExportServiceProvider"
```
Se crearán los siguientes archivos en tu proyecto:
- config/jexport.php
- app/Models/Export.php
- database/migrations/create_exports_table.php

#### Paso 4: Ejecutar migración
Esto creará una tabla llamada 'exports' en nuestra base de datos, la cuál tendrá el historial de todas las exportaciones realizadas.
```bash
php artisan migrate
```
## Uso
Puedes crear un archivo de exportación ejecutando el siguiente comando:
```bash
php artisan make:jexport UsersExport
```
Para el ejemplo exportaremos todos los usuarios creados entre un rango de fechas
```bash
<?php

namespace App\Exports;

use App\Models\User;

class UsersExport{

    public function query($start_date, $end_date){

        $data = User::whereBetween('created_at', [$start_date, $end_date])->get();

        return $data;
    }

}
```

En tu controlador:
```bash
use Jeanp\JExport\JExport;
```
```bash
$start_date = '2023-01-01';
$end_date = '2025-01-01';

JExport::dispatch(
  namespace: UsersExport::class,
  args: compact('start_date','end_date'),
);
```

Asegúrate de tener activado los queues de Laravel. Puedes ejecutar:

```bash
  php artisan queue:work --queue=exports
```


## Dependencias

 - [rap2hpoutre/fast-excel](https://github.com/rap2hpoutre/fast-excel)

