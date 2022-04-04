<?php

namespace App\Listeners;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Products;
use Settings;

/**
 * order's listener class
 */
class Orderlistener extends Injectable
{
    /**
     * fires a method if order is being created
     *
     * @param Event $event
     * @param \App\Components\Orderscomponent $component
     * @param [type] $postData
     * @return void
     */
    public function onCreate(Event $event, \App\Components\Orderscomponent $component, $postData)
    {
        // print_r($postData);
        $setting = new Settings();
        $settingData = $setting::findFirst('1');
        // if zipcode is empty
        if (!strlen($postData["zipcode"])) {
            // echo "zip is not set look for the default value";
            $postData["zipcode"] = $settingData->default_zipcode;
        }
        // echo "🔫 died in order listener";
        return $postData;
        die();
        $this->logger->info("from order After notifications");
    }
    /**
     * fires a method after order is created
     *
     * @param Event $event
     * @param \App\Components\Orderscomponent $component
     * @param [type] $postData
     * @return void
     */
    public function onCreated(Event $event, \App\Components\Orderscomponent $component, $postData)
    {
        // print_r($postData);
        $setting = new Settings();
        $settingData = $setting::findFirst('1');
        // if zipcode is empty
        if (!strlen($postData->zipcode)) {
            // echo "zip is not set look for the default value";
            $postData->zipcode = $settingData->default_zipcode;
        }
        // echo "🔫 died in order listener";
        $postData->save();
        die();
        $this->logger->info("from order After notifications");
    }
    // public function beforeCreate(Event $event, \App\Components\Orderscomponent $component)
    // {
    //     $this->logger->info("from order Before notifications");
    // }
}
