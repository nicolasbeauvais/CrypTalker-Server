<?php

namespace Controllers\Api;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;

/**
 * Class MessageController
 * @package Controllers\Api
 */
class MessageController extends AbstractApiController
{

    public function getIndex()
    {
        echo 'Hello World!';
    }
}
