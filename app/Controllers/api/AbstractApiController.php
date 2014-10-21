<?php

namespace Controllers\Api;

use Illuminate\Support\Facades\View;
use Controllers\AbstractController;

class AbstractApiController extends AbstractController
{

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
