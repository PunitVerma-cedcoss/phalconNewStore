<?php

namespace App\Console;

use Phalcon\Cli\Task;

/**
 * helper for token management in cli
 */
class TokenTask extends Task
{
    /**
     * returns a JWT token based on role
     *
     * @param [string] $role
     * @return void
     */
    public function getTokenAction($role)
    {
        $now = $this->datetime;
        $jwtinit = new \App\Components\JwtInit();
        $token = $jwtinit->init($role, $now, true);
        echo "[🆗] token generated 🔖" . PHP_EOL;
        echo $token . PHP_EOL;
    }
}
