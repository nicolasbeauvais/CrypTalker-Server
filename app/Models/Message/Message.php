<?php

namespace Models\Message;

use Models\AbstractModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;

/**
 * Class Message
 * @package Models\Room
 */
class Message extends AbstractModels
{
    public function newMessage($user_id, $room_id, $message)
    {
        $user_id = (int)$user_id;
        $room_id = (int)$room_id;

        $this->required('newMessage', $user_id, $room_id, $message);

        if (!$this->getModel('Room')->isInRoom($user_id, $room_id)) {
            $this->error(null, 'You\'re not in this room');
        }

        //@TODO: Send message to Google Cloud Messaging

        return $this->response();
    }
}
