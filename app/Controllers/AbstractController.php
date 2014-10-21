<?php

namespace Controllers;

use Illuminate\Routing\Controller;

/**
 * Class AbstractController
 * @package Controllers
 */
class AbstractController extends Controller
{
    /**
     * We define all models here for autocompletion
     */
    private $modelInstances = array();

    /**
     * @return \Models\User\User
     */
    public function getUser()
    {
        return $this->giveModel('User');
    }

    /**
     * @return \Models\Friend\Friend
     */
    public function getFriend()
    {
        return $this->giveModel('Friend');
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
}
