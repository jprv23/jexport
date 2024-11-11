<?php

return [

    'php' => env('JEXPORT_PHP','ea-php81'),
    'disk' => env('JEXPORT_DISK', 'public'),
    'directory' => env('JEXPORT_DIRECTORY', 'exports'),
    'queue' => env('JEXPORT_QUEUE', 'exports'),
    'memory_limit' => env('JEXPORT_MEMORY_LIMIT', '4072M'),

];
