<?php

namespace Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Models\User;

class AbstractController extends Controller {

    /**
     * We define all models here for autocompletion
     */
    private $modelInstances = array();

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->giveModel('User');
    }

    /**
     * Instantiate a model class
     *
     * @param $name
     * @return mixed
     */
    private function giveModel($name)
    {
        if (!isset($this->modelInstances[$name])) {
            $name = '\Models\\' . $name . '\\' . $name;
            $this->modelInstances[$name] = new $name;
        }

        return $this->modelInstances[$name];
    }

    /**
     * Do a json answer and die.
     *
     * @param $data
     */
    public static function answerJson($data)
    {
        echo View::make('json', array('response' => $data))->render();
        die;
    }

}
