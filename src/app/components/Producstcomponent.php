<?php

namespace App\Components;

use Phalcon\Di\Injectable;

/**
 * triggers product listener
 */
class Producstcomponent extends Injectable
{
    /**
     * fires onCreate event
     *
     * @param [object] $data
     * @return void
     */
    public function onCreate($data)
    {
        $eventsManager = $this->EventsManager;
        return $eventsManager->fire('productlistener:onCreate', $this, $data);
    }
}
