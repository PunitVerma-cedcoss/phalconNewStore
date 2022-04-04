<?php

namespace App\Components;

use Phalcon\Di\Injectable;
/**
 * triggers order listener
 */
class Orderscomponent extends Injectable
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
        return $eventsManager->fire('orderlistener:onCreate', $this, $data);
    }
}
