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

        if (!$this->getModel('Room')->isInRoom($user_id, $room_id)) {
            $this->error(null, 'You\'re not in this room');
        }

        $room_user_device_ids = $this->getModel('User')->getMobileIdByRoom($room_id, $user_id);

        $data = array(
            'type' => 'new_message',
            'date' => date('H:i:s Y-m-d'),
            'from_user' => $user_pseudo,
            'room_id' => $room_id
        );

        // Send message with GCM
        $response = GCM::make($room_user_device_ids, $message, $data);

        return $this->response();
    }

    public function test()
    {
        $message= 'Hello from push with GCM ! ' . date('H:m:s');
        $data = array(
            'type' => 'new_message',
            'date' => date('H:i:s Y-m-d'),
            'from_user' => 'TyTy',
            'room_id' => 1
        );

        // Send message with GCM
        $device_ids = array('APA91bHPr8hGCYqODIZUYSMLobojSpfqyCiBNOoDu3baadEZgWv_ep8RKaSG5xEMi9fQDRSYnBbUzfnT52EmMED-kxo0Egp7X3nv09mHX4UflBanWGp8tlT7Gdhw6J0EBl2SPFXcprknwEw18ZrBwyZPMHo0yX01-g');
        $response = GCM::make($device_ids, $message, $data);
        dd($response);

        return $this->response();
    }
}
