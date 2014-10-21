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

    private function isFriend($user_id, $user_friend_id)
    {
        return DB::table('friends')
            ->where('user_id', '=', $user_id)
            ->where('user_friend_id', '=', $user_friend_id, 'AND')
            ->first() === null ? false : true;
    }

    private function isFriendWaiting($user_id, $user_friend_id)
    {
        return DB::table('friends')
            ->where('user_id', '=', $user_id)
            ->where('user_friend_id', '=', $user_friend_id, 'AND')
            ->where('status', '=', 0, 'AND')
            ->first() === null ? false : true;
    }
}
