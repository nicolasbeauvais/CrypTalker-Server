<?php

namespace Controllers\Api;

use Illuminate\Support\Facades\Response;
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
     * Requires:
     * $_POST['pseudo']
     */
    public function postRequest()
    {
        $this->logged();

        $response = $this->getFriend()->request(Auth::user()->id, Input::get('pseudo'));

         return Response::json($response);
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

         return Response::json($response);
    }

    /**
     * Block a friend for the authenticated user.
     *
     * @param $user_friend_id
     */
    public function getBlock($user_friend_id)
    {
        $this->logged();

        $response = $this->getFriend()->block(Auth::user()->id, $user_friend_id);

         return Response::json($response);
    }

    /**
     * Unblock a friend for the authenticated user.
     *
     * @param $user_friend_id
     */
    public function getUnblock($user_friend_id)
    {
        $this->logged();

        $response = $this->getFriend()->unblock(Auth::user()->id, $user_friend_id);

         return Response::json($response);
    }
}
