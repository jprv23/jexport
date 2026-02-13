<?php

namespace Jeanp\JExport\Jobs;

use romanzipp\QueueMonitor\Traits\IsMonitored;

class JExportMonitoredJob extends JExportJob
{
    use IsMonitored;
}
