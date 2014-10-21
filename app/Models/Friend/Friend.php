<?php

namespace Models\Friend;

use Models\AbstractModels;
use Illuminate\Support\Facades\DB;

/**
 * Class Friend
 * @package Models\Friend
 */
class Friend extends AbstractModels
{

    public function request($user_id, $user_friend_id)
    {
        $user_id = (int)$user_id;
        $user_friend_id = (int)$user_friend_id;

        $this->required('request', $user_id, $user_friend_id);

        // What ?
        if ($user_id === $user_friend_id) {
            $this->error(null, 'You can\'t be friend with yourself');
            return $this->response();
        }

        // Already friend
        if ($this->isFriend($user_id, $user_friend_id)) {
            $this->error('user_friend_id', 'You are already friend with this user');
            return $this->response();
        }

        DB::table('friends')->insert(array(
            'user_id' => $user_id,
            'user_friend_id' => $user_friend_id,
            'status' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ));

        return $this->response();
    }

    public function accept($user_id, $user_friend_id)
    {
        $user_id = (int)$user_id;
        $user_friend_id = (int)$user_friend_id;

        $this->required('validate', $user_id, $user_friend_id);

        // What ?
        if ($user_id === $user_friend_id) {
            $this->error(null, 'You can\'t validate a friend request with yourself');
            return $this->response();
        }

        // No pending invitation
        if (!$this->isFriendWaiting($user_id, $user_friend_id)) {
            $this->error('user_friend_id', 'You don\'t have a pending invitation request with this user');
            return $this->response();
        }

        // They are now friend
        DB::table('friends')
            ->where('user_id', '=', $user_id)
            ->where('user_friend_id', '=', $user_friend_id, 'AND')
            ->update(array('status' => 1));

        DB::table('friends')->insert(array(
            'user_id' => $user_friend_id,
            'user_friend_id' => $user_id,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ));

        return $this->response();
    }

    public function block($user_id, $user_friend_id)
    {
        $user_id = (int)$user_id;
        $user_friend_id = (int)$user_friend_id;

        $this->required('validate', $user_id, $user_friend_id);

        // What ?
        if ($user_id === $user_friend_id) {
            $this->error(null, 'You can\'t block yourself');
            return $this->response();
        }

        // Already friend
        if (!$this->isFriend($user_id, $user_friend_id, 1)) {
            $this->error('user_friend_id', 'You can\'t block a users if he\'s not your friend');
            return $this->response();
        }

        $friendship = $this->getFriendShip($user_id, $user_friend_id);

        if ($friendship['blocked']) {
            $this->error('user_friend_id', 'You can\'t block a users if he\'s already blocked you');
            return $this->response();
        }

        // Block the user
        DB::table('friends')
            ->where('user_id', '=', $user_id)
            ->where('user_friend_id', '=', $user_friend_id, 'AND')
            ->update(array('status' => -1));

        return $this->response();
    }

    public function unblock($user_id, $user_friend_id)
    {
        $user_id = (int)$user_id;
        $user_friend_id = (int)$user_friend_id;

        $this->required('validate', $user_id, $user_friend_id);

        // What ?
        if ($user_id === $user_friend_id) {
            $this->error(null, 'You can\'t unblock yourself');
            return $this->response();
        }

        // Already friend
        if (!$this->isFriend($user_id, $user_friend_id, -1)) {
            $this->error('user_friend_id', 'You can\'t unblock a users you don\'t already blocked');
            return $this->response();
        }

        $friendship = $this->getFriendShip($user_id, $user_friend_id);

        if ($friendship['blocked'] && $friendship['friend']->status == -1) {
            $this->error('user_friend_id', 'You can\'t unblock a users if he blocked you');
            return $this->response();
        }

        // Block the user
        DB::table('friends')
            ->where('user_id', '=', $user_id)
            ->where('user_friend_id', '=', $user_friend_id, 'AND')
            ->update(array('status' => 1));

        return $this->response();
    }

    private function isFriend($user_id, $user_friend_id, $status = null)
    {
        $query = DB::table('friends')
            ->where('user_id', '=', $user_id)
            ->where('user_friend_id', '=', $user_friend_id, 'AND');

        if ($status !== null) {
            $query->where('status', '=', $status, 'AND');
        }

        $queryResponse = $query->first();

        return  $queryResponse === null ? false : true;
    }

    private function isFriendWaiting($user_id, $user_friend_id)
    {
        return DB::table('friends')
            ->where('user_id', '=', $user_id)
            ->where('user_friend_id', '=', $user_friend_id, 'AND')
            ->where('status', '=', 0, 'AND')
            ->first() === null ? false : true;
    }

    /**
     * Return an array with the both way friendship connection.
     *
     * @param $user_id
     * @param $user_friend_id
     *
     * @return array
     */
    private function getFriendShip($user_id, $user_friend_id)
    {
        $friendship = array();

        $friendship['user'] = DB::table('friends')
            ->where('user_id', '=', $user_id)
            ->where('user_friend_id', '=', $user_friend_id, 'AND')
            ->first();

        if ($friendship['user']->status == 0) {
            return $friendship;
        }

        $friendship['friend'] = DB::table('friends')
            ->where('user_id', '=', $user_friend_id)
            ->where('user_friend_id', '=', $user_id, 'AND')
            ->first();

        $friendship['blocked'] = ($friendship['user']->status == -1 || $friendship['user']->status == -1);

        return  $friendship;
    }
}
