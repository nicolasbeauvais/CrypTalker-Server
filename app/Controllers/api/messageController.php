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

    /**
     * Send a message to a room.
     *
     * Requires:
     * $_POST['room_id']
     * $_POST['message']
     *
     * @return mixed
     */
    public function postNew()
    {
        $this->logged();

        $response = $this->getMessage()->newMessage(Auth::user()->id, Input::get('room_id'), Input::get('message'));

        return View::make('json', array('response' => $response));
    }
}
