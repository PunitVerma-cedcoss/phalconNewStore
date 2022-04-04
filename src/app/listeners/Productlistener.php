<?php

namespace App\Listeners;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Products;
use Settings;

/**
 * order's listener class
 */
class Productlistener extends Injectable
{
    /**
     * fires a method if product is being created
     *
     * @param Event $event
     * @param \App\Components\Orderscomponent $component
     * @param [type] $postData
     * @return void
     */
    public function onCreate(Event $event, \App\Components\Producstcomponent $component, $postData)
    {
        $setting = new Settings();
        $settingData = $setting::findFirst('1');
        // if with tag is set
        if ($settingData->title_optimization == "with_tag") {
            $postData["name"] = implode(explode(" ", $postData["name"])) . implode(explode(",", $postData["tags"]));
        }

        // if product price is empty or zero then set default
        if ($postData["price"] == 0 || !strlen($postData["price"])) {
            $postData["price"] = $settingData->default_price;
        }
        // if product quantity is empty or zero then set default
        if ($postData["stock"] == 0 || !strlen($postData["stock"])) {
            $postData["stock"] = $settingData->default_stock;
        }
        // echo "🔫 died in order listener";
        return $postData;
        die();
        $this->logger->info("from Product After notifications");
    }
    /**
     * fires a method after product is created
     *
     * @param Event $event
     * @param \App\Components\Orderscomponent $component
     * @param [type] $postData
     * @return void
     */
    public function onCreated(Event $event, \App\Components\Producstcomponent $component, $postData)
    {
        $setting = new Settings();
        $settingData = $setting::findFirst('1');
        // if with tag is set
        if ($settingData->title_optimization == "with_tag") {
            $postData->name = implode(explode(" ", $postData->name)) . implode(explode(",", $postData->tags));
        }

        // if product price is empty or zero then set default
        if ($postData->price == 0 || !strlen($postData->price)) {
            $postData->price = $settingData->default_price;
        }
        // if product quantity is empty or zero then set default
        if ($postData->stock == 0 || !strlen($postData->stock)) {
            $postData->stock = $settingData->default_stock;
        }
        // echo "🔫 died in order listener";
        $postData->save();
        die();
        $this->logger->info("from Product After notifications");
    }
}
