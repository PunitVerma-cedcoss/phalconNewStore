<?php

use Phalcon\Mvc\Controller;


class AdminController extends Controller
{
    public function indexAction()
    {
        $lang  = $this->request->getquery()['locale'] ?? 'en';
        $this->view->t = $this->translator;
        // $this->assets->addJs('js/role.js');
        // $this->assets->addJs('js/lang.js');
        $list = new App\Components\Utilscomponent();
        $users = new Permissions();
        $this->view->data2 = $users::find();
        $this->view->token = $this->request->getQuery()['bearer'];
        $this->view->data = $list->getList();
        if ($this->request->isPost()) {
            $controllers = [];
            $actions = [];
            $aclData = [];
            foreach (array_keys($this->request->getPost()) as $keys) {
                if ($keys != 'name') {
                    $data = explode("<->", $keys);
                    $controller = substr($data[0], 0, strlen($data[0]) - strlen('Controller'));
                    $action = $data[1];
                    array_push($controllers, $controller);
                    array_push($actions, $action);
                    // echo $controller . "->" . $action . '<br>';
                    // $roles[$controller] = []
                }
            }
            foreach ($controllers as $c) {
                $aclData[$c] = [];
                foreach (array_keys($this->request->getPost()) as $i) {
                    if (preg_match("/{$c}/", $i)) {
                        array_push($aclData[$c], explode("Action", explode('<->', $i)[1])[0]);
                    }
                }
            }

            // push the acl data into db
            $permission = new Permissions();
            $s = $permission->assign(
                [
                    'role_name' => $this->request->getPost()['name'],
                    'permissions' => json_encode($aclData)
                ]
            );
            // echo "<pre>";
            // print_r($this->request->getPost()['name']);
            // echo "</pre>";
            // die();
            if ($s->save()) {
                header("location:/secure/mkACL?bearer=" . $this->request->getQuery()['bearer']);
            }
        }
    }
    public function viewrolesAction()
    {
    }
    public function editAction()
    {
        $lang  = $this->request->getquery()['locale'] ?? 'en';
        $this->view->t = $this->translator;
        // $this->assets->addJs('js/lang.js');
        if (isset($this->request->getquery()['name'])) {
            $this->assets->addJs('js/role.js');
            $list = new App\Components\Utilscomponent();
            $users = new Permissions();
            $this->view->data = $list->getList();
            $this->view->token = $this->request->getQuery()['bearer'];
            $this->view->name = $users::findFirst(
                [
                    'conditions' => 'role_name = :name:',
                    'bind' => [
                        'name' => $this->request->getQuery()["name"]
                    ]
                ]
            )->role_name;
            $this->view->data2 = json_decode($users::findFirst(
                [
                    'conditions' => 'role_name = :name:',
                    'bind' => [
                        'name' => $this->request->getQuery()["name"]
                    ]
                ]
            )->permissions);
        }


        if ($this->request->isPost()) {
            $controllers = [];
            $actions = [];
            $aclData = [];
            foreach (array_keys($this->request->getPost()) as $keys) {
                if ($keys != 'name') {
                    $data = explode("<->", $keys);
                    print_r($data);
                    $controller = substr($data[0], 0, strlen($data[0]) - strlen('Controller'));
                    $action = $data[1];
                    array_push($controllers, $controller);
                    array_push($actions, $action);
                }
            }
            foreach ($controllers as $c) {
                $aclData[$c] = [];
                foreach (array_keys($this->request->getPost()) as $i) {
                    if (preg_match("/{$c}/", $i)) {
                        array_push($aclData[$c], explode("Action", explode('<->', $i)[1])[0]);
                    }
                }
            }

            // push the acl data into db
            $permission = new Permissions();
            $perm = $permission::findFirst(
                [
                    'conditions' => 'role_name = :name:',
                    'bind' => [
                        'name' => $this->request->getPost()["name"]
                    ]
                ]
            );
            $s = $perm->assign(
                [
                    'role_name' => $this->request->getPost()['name'],
                    'permissions' => json_encode($aclData)
                ]
            );
            if ($s->save()) {
                // header("location:/secure/mkACL?role=admin");
                echo "saved";
            } else {
                echo "error";
            }
            header("location:/secure/mkACL?bearer=" . $this->request->getQuery()['bearer']);
            // die("died");
        }
    }
    public function loginAction()
    {
        $lang  = $this->request->getquery()['locale'] ?? 'en';
        $this->view->t = $this->translator;
        // $this->assets->addJs('js/lang.js');

        if ($this->request->isPost()) {
            print_r($this->request->getPost());

            $email = $this->escaper->sanitize($this->request->getPost()["email"]);
            $password = $this->escaper->sanitize($this->request->getPost()["password"]);

            $users = new Users();

            $userDbData = $users::findFirst(
                [
                    'conditions' => 'email = :email: AND password = :password:',
                    'bind' => [
                        'email' => $email,
                        'password' => $password,
                    ]
                ]
            );
            if ($userDbData) {
                if ($userDbData->count() == 1) {
                    // get the token and store in session
                    $now = new DateTimeImmutable();
                    $jwtinit = new \App\Components\JwtInit();
                    $token = $jwtinit->init($userDbData->role, $now, true);
                    $this->session->set("token", $token);
                    if ($userDbData->role == "admin") {
                        header("location:/admin?bearer=" . $token);
                    } else {
                        header("location:/product?bearer=" . $token);
                    }
                    die();
                } else {
                    $this->view->error = "incorrect details";
                }
            } else {
                $this->view->error = "incorrect details";
            }

            // echo $email;

            // die();
        }
    }
}
