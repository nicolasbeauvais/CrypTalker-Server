<?php

namespace Models\Room;

use Models\AbstractModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;

/**
 * Class Room
 * @package Models\User
 */
class Room extends AbstractModels
{
    /**
     * Create a room for the specified list of users ids.
     *
     * @param $user_id
     * @param $users_friend_id
     *
     * @return array
     */
    public function create($user_id, $users_friend_id)
    {
        $user_id = (int) $user_id;

        $this->required('create', $users_friend_id);

        $users_friend_id_verified = array();
        foreach ($users_friend_id as $id) {
            $users_friend_id_verified[] = (int) $id;
        }

        // Check if the user is friend with all the users in the list
        $nb_existing = DB::table('friends')
            ->where('user_id', '=', $user_id)
            ->whereIn('user_friend_id', $users_friend_id_verified)
            ->count();

        if ($nb_existing != count($users_friend_id_verified)) {
            $this->error(null, 'Invalid users ids');
            return $this->response();
        }

        // creation of the room
        $room_id = DB::table('rooms')->insertGetId(array(
            'name' => '',
            'key' => $this->randomString(40),
            'created_at' => date('Y-m-d H:i:s')
        ));

        $user_room = array();
        $users_friend_id_verified[] = $user_id;
        foreach ($users_friend_id_verified as $id) {
            $user_room[] = array(
                'user_id' => $id,
                'room_id' => $room_id,
                'created_at' => date('Y-m-d H:i:s')
            );
        }

        // Attach users to the room
        DB::table('user_room')->insert($user_room);

        return $this->response();
    }

    /**
     * Give a name to the specified room.
     *
     * @param $user_id
     * @param $room_id
     * @param $name
     *
     * @return array
     */
    public function name($user_id, $room_id, $name)
    {
        $user_id = (int) $user_id;
        $room_id = (int) $room_id;

        $this->required('create', $user_id, $room_id, $name);

        if (!$this->isInRoom($user_id, $room_id)) {
            $this->error(null, 'You must be in the room to change the room\'s name');
            return $this->response();
        }

        $validation = $this->validate('name', array('name' => $name));

        $this->parseValidation($validation);

        if (!$validation->validated) {
            return $this->response();
        }

        DB::table('rooms')
            ->where('id', '=', $room_id)
            ->update(array(
                'name' => $name
            ));

        return $this->response();
    }

    /**
     * Add a user to the specified room.
     *
     * @param $user_id
     * @param $user_friend_id
     * @param $room_id
     *
     * @return array
     */
    public function add($user_id, $user_friend_id, $room_id)
    {
        $user_id = (int) $user_id;
        $user_friend_id = (int) $user_friend_id;
        $room_id = (int) $room_id;

        $this->required('add', $user_id, $user_friend_id, $room_id);

        if ($this->isInRoom($user_friend_id, $room_id)) {
            $this->error(null, 'The user is already in the room');
            return $this->response();
        }

        if (!$this->getModel('Friend')->isFriend($user_id, $room_id, 1)
            || $this->getModel('Friend')->getFriendShip($user_id, $room_id)['blocked']) {
            $this->error(null, 'You can\'t add a user to the room if it\'s not your friend');
            return $this->response();
        }

        DB::table('user_room')->insert(array(
            'user_id' => $user_friend_id,
            'room_id' => $room_id,
            'created_at' => date('Y-m-d H:i:s')
        ));

        return $this->response();
    }

    /**
     * Remove a user to the specified room.
     *
     * @param $user_id
     * @param $room_id
     *
     * @return array
     */
    public function quit($user_id, $room_id)
    {
        $room_id = (int) $room_id;

        $this->required('quit', $room_id);

        if (!$this->isInRoom($user_id, $room_id)) {
            $this->error(null, 'You\'re not in this room');
            return $this->response();
        }

        DB::table('user_room')
            ->where(array('user_id' => $user_id, 'room_id' => $room_id))
            ->delete();

        return $this->response();
    }

    /**
     * Check if a user is in a room.
     *
     * @param $user_id
     * @param $room_id
     *
     * @return bool
     */
    public function isInRoom($user_id, $room_id)
    {
        return DB::table('user_room')
            ->where('user_id', '=', $user_id)
            ->where('room_id', '=', $room_id, 'AND')
            ->first() === null ? false : true;
    }
}
