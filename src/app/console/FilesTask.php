<?php

namespace App\Console;

use Phalcon\Cli\Task;
/**
 * class for managing file system
 */
class FilesTask extends Task
{
    /**
     * deletes a log file if present
     *
     * @return void
     */
    public function removeLogsAction()
    {
        unlink(BASE_PATH . "/storage/log/main.log");
        echo "[🆗] log file main deleted 🔖" . PHP_EOL;
    }
    /**
     * deletes cache file from the dir
     *
     * @return void
     */
    public function removecacheAction()
    {
        unlink(APP_PATH . "/security/acl.cache");
        echo "[🆗] ACL cache file deleted 🔖" . PHP_EOL;
    }
}
