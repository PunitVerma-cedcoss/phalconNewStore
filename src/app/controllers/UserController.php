<?php

use App\Components\JwtInit;
use Phalcon\Mvc\Controller;

class UserController extends Controller
{
    public function indexAction()
    {
        // die();
        $user = new Users();
        $roleData = $user::find();
        $this->view->data = $roleData;
        $this->view->token = $this->session->get('token');
        $this->view->t = $this->translator;

        // if got post
        if ($this->request->isPost()) {
            $user = new Users();
            foreach (array_keys($this->request->getPost()) as $ids) {
                $userdata = $user::findFirst(
                    [
                        'conditions' => 'id = :id:',
                        'bind' => [
                            'id' => $ids
                        ]
                    ]
                );
                // print_r($userdata->email);
                $userdata->delete();
                header("location:/user?bearer=" . $this->session->get('token'));
                // die();
            }
        }
    }
}
