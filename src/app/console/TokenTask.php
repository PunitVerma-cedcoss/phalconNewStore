<?php

namespace App\Console;

use Phalcon\Cli\Task;

class TokenTask extends Task
{
    public function getTokenAction($role)
    {
        $now = $this->datetime;
        $jwtinit = new \App\Components\JwtInit();
        $token = $jwtinit->init($role, $now, true);
        echo "[🆗] token generated 🔖" . PHP_EOL;
        echo $token . PHP_EOL;
    }
}
