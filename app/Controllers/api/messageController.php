<?php

namespace Controllers\Api;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;

class MessageController extends AbstractApiController
{

    public function getIndex()
    {
        echo 'Hello World!';
    }
}
