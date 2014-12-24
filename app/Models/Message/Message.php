<?php

namespace Models\Message;

use Cryptalker\Google\GCM;
use Models\AbstractModels;

/**
 * Class Message
 * @package Models\Room
 */
class Message extends AbstractModels
{
    public function newMessage($user_id, $user_pseudo, $room_id, $message)
    {
        $user_id = (int)$user_id;
        $room_id = (int)$room_id;

        $this->required('newMessage', $user_id, $room_id, $message);

        if (!$this->getRoom()->isInRoom($user_id, $room_id)) {
            $this->error(null, 'You\'re not in this room');
        }

        $room_user_device_ids = $this->getUser()->getMobileIdByRoom($room_id, $user_id);

        $data = array(
            'type' => 'new_message',
            'date' => date('H:i:s Y-m-d'),
            'from_user' => $user_pseudo,
            'room_id' => $room_id
        );

        // Send message with GCM
        $response = GCM::make($room_user_device_ids, $message, $data);

        $this->data($response);

        return $this->response();
    }
}
