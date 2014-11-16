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

    /**
     * Make a friend request for the authenticated user.
     *
     * @param $user_id
     * @param $pseudo
     *
     * @return array
     */
    public function request($user_id, $pseudo)
    {
        $user_id = (int)$user_id;

        $this->required('request', $user_id, $pseudo);

        $user_friend = $this->getByPseudo($pseudo);

        // Pseudo doesn't exist
        if (!$user_friend) {
            $this->error('pseudo', 'There is no user with the pseudo ' . $pseudo);
            return $this->response();
        }

        // What ?
        if ($user_id === (int)$user_friend->id) {
            $this->error('pseudo', 'You can\'t be friend with yourself');
            return $this->response();
        }

        // Already friend
        if ($this->isFriend($user_id, $user_friend->id)) {
            $this->error('pseudo', 'You are already friend with this user');
            return $this->response();
        }

        DB::table('friends')->insert(array(
            'user_id' => $user_id,
            'user_friend_id' => $user_friend->id,
            'status' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ));

        return $this->response();
    }

    /**
     * Accept a friend request for the authenticated user.
     *
     * @param $user_id
     * @param $user_friend_id
     *
     * @return array
     */
    public function accept($user_id, $user_friend_id)
    {
        $user_id = (int)$user_id;
        $user_friend_id = (int)$user_friend_id;

        $this->required('accept', $user_id, $user_friend_id);

        // What ?
        if ($user_id === $user_friend_id) {
            $this->error(null, 'You can\'t accept a friend request with yourself');
            return $this->response();
        }

        // No pending invitation
        if (!$this->isFriendWaiting($user_friend_id, $user_id)) {
            $this->error('user_friend_id', 'You don\'t have a pending invitation request with this user');
            return $this->response();
        }

        // They are now friend
        DB::table('friends')
            ->where('user_id', '=', $user_friend_id)
            ->where('user_friend_id', '=', $user_id, 'AND')
            ->update(array('status' => 1));

        DB::table('friends')->insert(array(
            'user_id' => $user_id,
            'user_friend_id' => $user_friend_id,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ));

        $this->getModel('Room')->create($user_id, (array)$user_friend_id, true);

        return $this->response();
    }

    /**
     * Deny a friend request for the authenticated user.
     *
     * @param $user_id
     * @param $user_friend_id
     *
     * @return array
     */
    public function refuse($user_id, $user_friend_id)
    {
        $user_id = (int)$user_id;
        $user_friend_id = (int)$user_friend_id;

        $this->required('deny', $user_id, $user_friend_id);

        // What ?
        if ($user_id === $user_friend_id) {
            $this->error(null, 'You can\'t deny a friend request with yourself');
            return $this->response();
        }

        // No pending invitation
        if (!$this->isFriendWaiting($user_friend_id, $user_id)) {
            $this->error('user_friend_id', 'You don\'t have a pending invitation request with this user');
            return $this->response();
        }

        // Simply delete the request
        DB::table('friends')
            ->where('user_id', '=', $user_friend_id)
            ->where('user_friend_id', '=', $user_id, 'AND')
            ->delete();

        return $this->response();
    }

    /**
     * Block a friend for the authenticated user.
     *
     * @param $user_id
     * @param $user_friend_id
     *
     * @return array
     */
    public function block($user_id, $user_friend_id)
    {
        $user_id = (int)$user_id;
        $user_friend_id = (int)$user_friend_id;

        $this->required('block', $user_id, $user_friend_id);

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

    /**
     * Unblock a friend for the authenticated user.
     *
     * @param $user_id
     * @param $user_friend_id
     *
     * @return array
     */
    public function unblock($user_id, $user_friend_id)
    {
        $user_id = (int)$user_id;
        $user_friend_id = (int)$user_friend_id;

        $this->required('unblock', $user_id, $user_friend_id);

        // What ?
        if ($user_id === $user_friend_id) {
            $this->error(null, 'You can\'t unblock yourself');
            return $this->response();
        }

        // Already friend
        if (!$this->isFriend($user_id, $user_friend_id, -1)) {
            $this->error('user_friend_id', 'You can\'t unblock a users you didn\'t already blocked');
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

    /**
     * Check if a user is friend with a another.
     *
     * Can be checked with a special friend status.
     *
     * @param $user_id
     * @param $user_friend_id
     * @param null $status
     *
     * @return bool
     */
    public function isFriend($user_id, $user_friend_id, $status = null)
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

    /**
     * Check if the user has a pending friend request.
     *
     * @param $user_id
     * @param $user_friend_id
     *
     * @return bool
     */
    public function isFriendWaiting($user_id, $user_friend_id)
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
    public function getFriendShip($user_id, $user_friend_id)
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

    /**
     * Get a user by pseudo (not case sensitive)
     *
     * @param $pseudo
     * @return bool
     */
    private function getByPseudo($pseudo)
    {
        if (!$pseudo) {
            return false;
        }

        $pseudo = strtolower($pseudo);

        return DB::table('users')->where(DB::raw('LOWER(pseudo)'), '=', $pseudo)->first();
    }
}
