<?php

namespace App\Console;

use Phalcon\Cli\Task;

class MainTask extends Task
{
    public function mainAction()
    {
        echo "this is default task and the defualt action" . PHP_EOL;
        $this->logger;
    }
}
