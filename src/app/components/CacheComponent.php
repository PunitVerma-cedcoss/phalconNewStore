<?php

namespace App\Components;

use Exception;
use Phalcon\Di\Injectable;
use Phalcon\Cache;
use Phalcon\Cache\CacheFactory;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Storage\SerializerFactory;

use Phalcon\Cache\Adapter\Stream;

class CacheComponent extends Injectable
{
    public function initCache($key = '', $val = '')
    {
        $serializerFactory = new SerializerFactory();

        $options = [
            'defaultSerializer' => 'Php',
            'lifetime'          => 7200,
            'storageDir'        => BASE_PATH . '/storage/cache',
        ];

        $adapter = new Stream($serializerFactory, $options);


        // $serializerFactory = new SerializerFactory();

        // $options = [
        //     'defaultSerializer' => 'Php',
        //     'lifetime'          => 7200,
        //     'host'              => '127.0.0.1',
        //     'port'              => 6379,
        //     'index'             => 1,
        // ];

        // $adapter = new Redis($serializerFactory, $options);

        $cache = new Cache($adapter);

        // $cache->set($key, $val);

        return $cache;
    }
}
