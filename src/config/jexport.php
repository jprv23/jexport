<?php

return [
    //General settings
    'php' => env('JEXPORT_PHP','ea-php81'),
    'disk' => env('JEXPORT_DISK', 'public'),
    'directory' => env('JEXPORT_DIRECTORY', 'exports'),
    'queue' => env('JEXPORT_QUEUE', 'exports'),
    'connection' => env('JEXPORT_CONNECTION', 'mysql'), // null means default connection
    'memory_limit' => env('JEXPORT_MEMORY_LIMIT', '4072M'),
    'time_limit' => env('JEXPORT_TIME_LIMIT', 0), // 0 means no limit
    'max_execution_time' => env('JEXPORT_MAX_EXECUTION_TIME', 600),

    'is_monitored' => env('JEXPORT_IS_MONITORED', false),

    //View settings
    'view_title' => 'Reportes Generados',
    'view_interval_seconds' => 3,
];
