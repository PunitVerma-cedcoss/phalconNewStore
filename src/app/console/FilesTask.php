<?php

namespace App\Console;

use Phalcon\Cli\Task;

class FilesTask extends Task
{
    public function removeLogsAction()
    {
        unlink(BASE_PATH . "/storage/log/main.log");
        echo "[🆗] log file main deleted 🔖" . PHP_EOL;
    }
    public function removecacheAction()
    {
        unlink(APP_PATH . "/security/acl.cache");
        echo "[🆗] ACL cache file deleted 🔖" . PHP_EOL;
    }
}
