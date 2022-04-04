<?php

namespace App\Listeners;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
/**
 * listener class , fires everytime on hit
 */
class Notificationslistener extends Injectable
{
    /**
     * authenticaltes a token
     *
     * @param Event $event
     * @param \Phalcon\Mvc\Application $application
     * @return void
     */
    public function beforeHandleRequest(Event $event, \Phalcon\Mvc\Application $application)
    {
        $aclFile = APP_PATH . '/security/acl.cache';

        // get controller and action name
        $getData = explode("/", $this->request->getQuery()['_url']);
        $controllerName = $this->router->getControllerName();
        $actionName = strlen($this->router->getActionName()) != 0 ? $this->router->getActionName() : 'index';
        $role = $application->request->get('bearer');
        // now check in db for valid creds
        //if acl file exists
        //if login screen is hit , just proceed 💱
        if ($controllerName != 'auth' && ($controllerName != 'admin' && $actionName != 'login')) {
            if (!isset($role)) {
                die("please prodive bearer as a token");
            }
            // call validator of token
            $validator = new \App\Components\JwtInit();
            $resp = $validator->firebaseJwtValidate($role);
            if ($resp) {
                if (is_file($aclFile)) {
                    $acl = unserialize(file_get_contents($aclFile));
                    $role = $resp;
                    if (!$acl->isAllowed($role, $controllerName, $actionName)) {
                        echo "Acess Denied " . $role;
                        die();
                    }
                }
            }
        }
    }
}
