<?php

namespace App\Components;

/**
 * helper class of usefull functions
 */
class Utilscomponent
{
    /**
     * returns a list of components and their actions in a assoc array
     *
     * @return array
     */
    public function getList()
    {
        $controllers = [];

        foreach (glob(APP_PATH . '/controllers/*Controller.php') as $controller) {
            // echo $controller;
            $className = basename($controller, '.php');
            $controllers[$className] = [];
            $methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                if (\Phalcon\Text::endsWith($method->name, 'Action')) {
                    $controllers[$className][] = $method->name;
                }
            }
        }
        return $controllers;
    }
    /**
     * returns a dashboard
     *
     * @return string
     */
    public function getDashboard()
    {
        $nav = '
            <div class="sidebar flex flex-col col-span-2 md:col-span-3 lg:col-span-2 h-screen text-gray-200 bg-indigo-500">
            <div class="logo flex justify-center items-center p-3">
                <i class="fa fa-bolt mr-1"></i>
                <p class="text-xl font-medium lg:text-xl md:text-sm"><?php echo $t->_("name-dashboard", ["name" => "punit"]); ?></p>
            </div>
            <div class="tiles grow capitalize">
                <div class="tile p-2">
                    <p class="text-sm text-gray-300 my-2"><?php echo $t->_("dashboard"); ?></p>
                    <a href="#">
                        <div class="tile-option flex bg-white text-indigo-500 p-2 rounded-lg justify-start items-center">
                            <i class="fa fa-box mr-2"></i>
                            <p><?php echo $t->_("dashboard"); ?></p>
                        </div>
                    </a>
                </div>
                <div class="tile p-2 text-sm">
                    <p class="text-sm text-gray-300 my-2"><?php echo $t->_("dashboard"); ?></p>
                    <a href="#">
                        <div class="tile-option flex hover:bg-white hover:text-indigo-500 p-2 rounded-lg justify-around items-center">
                            <i class="fa fa-key mr-2"></i>
                            <p class="grow"><?php echo $t->_("set roles"); ?></p>
                            <i class="fa fa-angle-right text-xs text-white"></i>
                        </div>
                    </a>
                    <a href="#">
                        <div class="tile-option flex hover:bg-white hover:text-indigo-500 p-2 rounded-lg justify-start items-center">
                            <i class="fa fa-users mr-2"></i>
                            <p><?php echo $t->_("users permissions"); ?></p>
                        </div>
                    </a>
                    <a href="/secure/mkACL?bearer=<?php echo $token; ?>">
                        <div class="tile-option flex hover:bg-white hover:text-indigo-500 p-2 rounded-lg justify-start items-center">
                            <i class="fa fa-bolt mr-2"></i>
                            <p><?php echo $t->_("build ACL"); ?></p>
                        </div>
                    </a>
                    <a href="/product?bearer=<?php echo $token; ?>">
                        <div class="tile-option flex hover:bg-white hover:text-indigo-500 p-2 rounded-lg justify-start items-center">
                            <i class="fa fa-comment mr-2"></i>
                            <p><?php echo $t->_("product"); ?></p>
                        </div>
                    </a>
                    <a href="/order?bearer=<?php echo $token; ?>">
                        <div class="tile-option flex hover:bg-white hover:text-indigo-500 p-2 rounded-lg justify-start items-center">
                            <i class="fa fa-gift mr-2"></i>
                            <p><?php echo $t->_("Order"); ?></p>
                        </div>
                    </a>
                    <a href="/admin/logout?bearer=<?php echo $token; ?>">
                        <div class="tile-option flex hover:bg-white hover:text-indigo-500 p-2 rounded-lg justify-start items-center">
                            <i class="fa fa-power-off mr-2"></i>
                            <p><?php echo $t->_("Log out"); ?></p>
                        </div>
                    </a>
                </div>
            </div>
            <div class="border-t bg-gradient-to-t from-indigo-500 to-indigo-600 flex justify-around">
                <div class=" p-3 flex justify-center items-center">
                    <i class="fa fa-cog"></i>
                </div>
                <div class=" p-3 flex justify-center items-center">
                    <i class="fa fa-heart"></i>
                </div>
                <div class=" p-3 flex justify-center items-center">
                    <i class="fa fa-power-off"></i>
                </div>
                <div class=" p-3 flex justify-center items-center">
                    <i class="fa fa-bolt"></i>
                </div>
            </div>
        </div>
            ';
        return $nav;
    }
}
