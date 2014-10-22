<?php

namespace Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;

/**
 * Class RoomController
 * @package Controllers\Api
 */
class RoomController extends AbstractApiController
{
    /**
     * Create a room for the specified list of users ids.
     *
     * Requires:
     * $_POST['users_id']
     *
     * @return mixed
     */
    public function postCreate()
    {
        $this->logged();

        $response = $this->getRoom()->create(Auth::user()->id, Input::get('users_id'));

        return View::make('json', array('response' => $response));
    }

    /**
     * Give a name to the specified room.
     *
     * Requires:
     * $_POST['room_id']
     * $_POST['name']
     *
     * @return mixed
     */
    public function postName()
    {
        $this->logged();

        $response = $this->getRoom()->name(Auth::user()->id, Input::get('room_id'), Input::get('name'));

        return View::make('json', array('response' => $response));
    }

    /**
     * Add a user to the specified room.
     *
     * @param $user_friend_id
     * @param $room_id
     *
     * @return mixed
     */
    public function getAdd($user_friend_id, $room_id)
    {
        $this->logged();

        $response = $this->getRoom()->add(Auth::user()->id, $user_friend_id, $room_id);

        return View::make('json', array('response' => $response));
    }

    /**
     * Remove a user of the specified room.
     *
     * @param $room_id
     *
     * @return mixed
     */
    public function getQuit($room_id)
    {
        $this->logged();

        $response = $this->getRoom()->quit(Auth::user()->id, $room_id);

        return View::make('json', array('response' => $response));
    }
}
