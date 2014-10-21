<?php

namespace Controllers\Api;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

/**
 * Class FriendController
 * @package Controllers\Api
 */
class FriendController extends AbstractApiController
{
    /**
     * Make a friend request for the authenticated user.
     *
     * @param int $user_friend_id
     */
    public function getRequest($user_friend_id)
    {
        $this->logged();

        $response = $this->getFriend()->request(Auth::user()->id, $user_friend_id);

        return View::make('json', array('response' => $response));
    }

    /**
     * Accept a friend request for the authenticated user.
     *
     * @param $user_friend_id
     */
    public function getAccept($user_friend_id)
    {
        $this->logged();

        $response = $this->getFriend()->accept(Auth::user()->id, $user_friend_id);

        return View::make('json', array('response' => $response));
    }

    /**
     * Block a friend for the authenticated user.
     *
     * @param $user_friend_id
     */
    public function getBlock($user_friend_id)
    {
        $this->logged();
    }
}
