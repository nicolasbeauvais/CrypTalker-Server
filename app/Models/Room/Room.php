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
     * @param $users_id
     *
     * @return array
     */
    public function create($user_id, $users_id)
    {
        $user_id = (int) $user_id;

        $this->required('create', $users_id);

        $users_id_verified = array();
        foreach ($users_id as $id) {
            $users_id_verified[] = (int) $id;
        }

        // Check if the user is friend with all the users in the list
        $nb_existing = DB::table('friends')
            ->where('user_id', '=', $user_id)
            ->whereIn('user_friend_id', $users_id_verified)
            ->count();

        if ($nb_existing != count($users_id_verified)) {
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
        foreach ($users_id_verified as $id) {
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
}
