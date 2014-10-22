<?php

namespace Cryptalker\Google;

use Illuminate\Support\Facades\Config;
use Exception;

/**
 * Class GCM
 * @package Cryptalker\Google
 */
class GCM
{
    private static $gcm_url = 'https://android.googleapis.com/gcm/send';

    /**
     * Send a push notification
     *
     * @param $registration_ids
     * @param array $data
     *
     * @return bool
     */
    public static function send_notification($registration_ids, array $data)
    {
        $fields = array(
            'registration_ids' => $registration_ids,
            'data' => json_encode($data)
        );

        $headers = array(
            'Authorization: key=' . Config::get('external.google.cloud_messaging_key'),
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, self::$gcm_url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            return false;
        }

        // Close connection
        curl_close($ch);

        return true;
    }
}
