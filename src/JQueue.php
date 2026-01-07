<?php

namespace Jeanp\JExport;

class JQueue
{
    protected function php_path()
    {
        $host = request()->getHost();
        if ($host == 'localhost') {
            return 'php';
        }

        return config('jexport.php', '/usr/local/bin/ea-php81');
    }

    public function start($queues = [])
    {
        if(count($queues) == 0){
            return;
        }

        $base = base_path();
        $php_path = self::php_path();

        $count_queue = 0;
        $array_cmd_queue = [];
        $max_execution_time = config('jexport.max_execution_time', 600);
        foreach ($queues as $key => $queue) {
            $status_queue = $this->cmd_is_queue($queue);
            if (!$status_queue) {
                $array_cmd_queue[] = "{$php_path} artisan queue:work --queue={$queue} --memory=6144 --timeout={$max_execution_time} > /dev/null 2>&1";
                $count_queue++;
            }
        }
        if ($count_queue > 0) {
            $cmd_queue = implode(" & ", $array_cmd_queue);
            $cmd = "cd {$base}; {$cmd_queue} &";

            shell_exec($cmd);
        }
        return $count_queue;
    }

    protected function cmd_is_queue($name = 'send_wsp', $bool = true)
    {
        // Comando de cola que estás ejecutando
        $command = 'queue=' . $name;
        $processes = $this->commands_to_array($command, $bool);
        // Log::info('queuessaa=' . $name,$processes);
        if ($bool) {
            $processCount = count($processes);
            return ($processCount > 0);
        }
        return $processes;
    }

    protected function commands_to_array($command, $bool)
    {
        // Ejecutar el comando y capturar la salida
        // ps -eo pid,command | grep 'queue=send_wsp_cinco' | grep -v grep
        if ($bool) {
            $output = shell_exec("ps -eo pid,command | grep '{$command}' | grep -v grep");
        } else {
            $output = shell_exec("ps -e -o pid,pcpu,pmem,command | grep '{$command}' | grep -v grep");
            // dd($output);
        }

        // Inicializar array para almacenar los procesos
        $processes = [];
        // Dividir la salida en líneas y procesar cada línea
        $lines = explode("\n", trim($output)); // Eliminar cualquier espacio en blanco adicional
        foreach ($lines as $line) {
            // Dividir cada línea en columnas (PID y COMMAND)
            $columns = preg_split('/\s+/', $line, 4); // Dividir en dos partes, máximo 2 columnas
            if (count($columns) == 4) {
                // Crear un array asociativo con claves 'pid' y 'command' y agregarlo al array de procesos
                $processes[] = [
                    'pid' => $columns[0],
                    'pcpu' => $columns[1],
                    'pmem' => $columns[2],
                    'command' => $columns[3],
                ];
            }
        }
        // dd($processes);
        return $processes;
    }

    protected function free_memory()
    {
        $host = request()->getHost();
        if ($host == 'localhost') {
            return (object)[
                'mb_total' => 100,
                'mb_used' => 0,
                'porcent_total' => 100,
                'porcent_used' => 0,
            ];
        }
        $total_memory = 8 * 1024; //8GB
        $output = (float) shell_exec('
    TOTAL=$(free | awk \'/Mem:/ { print $2 }\')
    for USER in $(ps haux | awk \'{print $1}\' | sort -u)
    do
        ps hux -U $USER | awk -v user=$USER -v total=$TOTAL \'{ sum += $6 } END { printf "%.2f",  sum/1024; }\'
    done
    ');
        if (!$output) {
            $output = 1;
        }
        return (object)[
            'mb_total' => number_format($total_memory, 2, '.', '') / 1,
            'mb_used' => number_format($output, 2, '.', '') / 1,
            'porcent_total' => 100,
            'porcent_used' => number_format($output / $total_memory * 100, 2, '.', '') / 1,
        ];
    }

    protected function command_kill($pid)
    {
        return shell_exec("kill -9 {$pid}");
    }
}
