<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream as AdaStream;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Router;

use Phalcon\Logger\Adapter\Stream;
use Phalcon\Logger\AdapterFactory;
use Phalcon\Logger\LoggerFactory;

use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream as SesStream;

$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

require_once BASE_PATH . '/vendor/autoload.php';


// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

$loader->registerNamespaces(
    [
        'App\Components' => APP_PATH . '/components',
        'App\Listeners' => APP_PATH . '/listeners'
    ]
);

$loader->register();

$container = new FactoryDefault();


// setting logger 🎴
$adapter = new AdaStream('../storage/log/main.log');
$logger = new Logger(
    'messages',
    [
        'main' => $adapter,
    ]
);
$container->set('logger', $logger);

//setting listeners
$eventsManager = new EventsManager();
$eventsManager->attach('orderlistener', new App\Listeners\Orderlistener());
$eventsManager->attach('productlistener', new App\Listeners\Productlistener());
$eventsManager->attach('application:beforeHandleRequest', new App\Listeners\Notificationslistener());
$container->set(
    'EventsManager',
    $eventsManager
);


$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$container->set(
    'router',
    function () {
        $router = new Router();
        $router->handle($_GET['_url']);
        return $router;
    }
);

$container->set(
    'datetime',
    function () {
        $now = new DateTimeImmutable();
        return $now;
    }
);

$container->set(
    'escaper',
    function () {
        $escaper = new App\Components\Myescaper();
        return $escaper;
    }
);

//cache container
// $container->setShared(
//     'cache',
//     function () {
//         $cache = new \App\Components\CacheComponent();
//         $cache = $cache->initCache();
//         return $cache;
//     }
// );
//cache container
$container->setShared(
    'cache',
    function () {
        $cache = new \App\Components\CacheComponent();
        $cache = $cache->initCache();
        return $cache;
    }
);

//setting session
$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new SesStream(
            [
                'savePath' => '/tmp',
            ]
        );
        $session
            ->setAdapter($files)
            ->start();
        return $session;
    }
);

$container->set(
    'translator',
    function () {
        $lang  = $this->getRequest()->getquery()['locale'] ?? 'en';
        $cache = $this->getCache();
        if ($cache->has($lang)) {
            return $cache->get($lang);
        } else {
            $transComponentObject = new App\Components\LocaleComponent();
            $cache->set($lang, $transComponentObject->getTranslator($lang));
            return $cache->get($lang);
        }
    }
);



//logger
$container->set(
    'logger',
    function () {
        $adapter = new Stream(BASE_PATH . '/storage/log/main.log');
        $logger  = new Logger(
            'messages',
            [
                'main' => $adapter,
            ]
        );
        return $logger;
    }
);


$application = new Application($container);

$eventsManager->fire('application:beforeHandleRequest', $application);

$container->set(
    'db',
    function () {
        return new Mysql(
            [
                'host'     => 'mysql-server',
                'username' => 'root',
                'password' => 'secret',
                'dbname'   => 'shop',
            ]
        );
    }
);



// $container->set(
//     'mongo',
//     function () {
//         $mongo = new MongoClient();

//         return $mongo->selectDB('phalt');
//     },
//     true
// );

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
