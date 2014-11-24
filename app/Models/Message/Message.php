<?php

namespace Models\Message;

use Cryptalker\Google\GCM;
use Illuminate\Support\Facades\Log;
use Models\AbstractModels;
use Illuminate\Support\Facades\DB;
use Exception;

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

        $room_user_ids = $this->getModel('User')->getMobileIdByRoom($room_id, $user_id);

        $data = array(
            'type' => 'new_message',
            'date' => date('H:i:s Y-m-d'),
            'from_user' => $user_pseudo,
            'room_id' => $room_id
        );

        // Send message with GCM
        $gcm = new GCM();
        $gcm->setDevices($room_user_ids);
        $response = $gcm->send($message, $data);

        Log::info($response ? 'true' : 'false');

        return $this->response();
    }

    public function test()
    {
        $message= 'Hello from push with GCM !';
        $data = array(
            'type' => 'new_message',
            'date' => date('H:i:s Y-m-d'),
            'from_user' => 'TyTy',
            'room_id' => 1
        );

        // Send message with GCM
        $gcm = new GCM();
        $gcm->setDevices(array('APA91bHPr8hGCYqODIZUYSMLobojSpfqyCiBNOoDu3baadEZgWv_ep8RKaSG5xEMi9fQDRSYnBbUzfnT52EmMED-kxo0Egp7X3nv09mHX4UflBanWGp8tlT7Gdhw6J0EBl2SPFXcprknwEw18ZrBwyZPMHo0yX01-g'));
        $response = $gcm->send($message, $data);
        dd($response);
        return $response;
    }
}
