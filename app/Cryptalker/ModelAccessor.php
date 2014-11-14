<?php

namespace Cryptalker;

trait ModelAccessor
{
    /**
     * We define all models here to improve IDE autocompletion
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
     * @return \Models\Room\Room
     */
    public function getRoom()
    {
        return $this->giveModel('Room');
    }

    /**
     * @return \Models\Message\Message
     */
    public function getMessage()
    {
        return $this->giveModel('Message');
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
