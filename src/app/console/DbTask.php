<?php

namespace App\Console;

use Orders;
use Phalcon\Cli\Task;
use Products;
use Settings;
/**
 * helper class for database operations on cli
 */
class DbTask extends Task
{
    /**
     * set default price
     *
     * @param [int] $price
     * @return void
     */
    public function setPriceAction($price)
    {
        $settings = new Settings();
        $s = $settings::findFirst();
        $s->default_price = $price;
        if ($s->save()) {
            echo "[🆗] {$price} as default price is set";
        } else {
            echo "some error occured 🌍";
        }
        echo PHP_EOL;
    }
    /**
     * set default stock
     *
     * @param [int] $stock
     * @return void
     */
    public function setStockAction($stock)
    {
        $settings = new Settings();
        $s = $settings::findFirst();
        $s->default_stock = $stock;
        if ($s->save()) {
            echo "[🆗] {$stock} as default stock is set";
        } else {
            echo "some error occured 🌍";
        }
        echo PHP_EOL;
    }
    /**
     * returns stock count
     *
     * @return void
     */
    public function getStockCountAction()
    {
        $product = new Products();
        $s = $product::find(
            [
                "conditions" => "product_stock < :ps:",
                "bind" => [
                    "ps" => 10
                ]
            ]
        );
        print_r($s->Count());
        echo PHP_EOL;
    }
    /**
     * returns array of products
     *
     * @return void
     */
    public function getAction()
    {
        $orders = new Orders();
        $s = $orders::find(
            [
                "conditions" => "created_at LIKE :date:",
                "bind" => [
                    "date" => "%" . date('Y-m-d') . "%"
                ]
            ]
        );
        print_r($s->Count());
        echo PHP_EOL;
    }
}
