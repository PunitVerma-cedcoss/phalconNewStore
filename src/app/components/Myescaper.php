<?php

namespace App\Components;

use Phalcon\Escaper;

/**
 * helper class to manage escaper
 */
class Myescaper
{
    /**
     * returns a sanitized variable
     *
     * @param [string] $var
     * @return string
     */
    public function sanitize($var)
    {
        $escaper = new Escaper();
        return $escaper->escapeHtml($var);
    }
}
