<?php

namespace Cryptalker\Google;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Class GCM
 * @package Cryptalker\Google
 */
class GCM {
    var $url = 'https://android.googleapis.com/gcm/send';
    var $serverApiKey;
    var $devices = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->serverApiKey = Config::get('external.google.cloud_messaging_key');
    }
    /**
     * Set the devices to send to
     * @param $deviceIds array of device tokens to send to
     */
    public function setDevices($deviceIds)
    {

        if(is_array($deviceIds)){
            $this->devices = $deviceIds;
        } else {
            $this->devices = array($deviceIds);
        }
    }

    /**
     * Send the message to the device.
     *
     * @param string $message the message to send
     * @param array $data Array of data to accompany the message
     *
     * @return bool
     */
    public function send($message, $data)
    {

        if(!is_array($this->devices) || count($this->devices) == 0){
            $this->error("No devices set");
        }

        if(strlen($this->serverApiKey) < 8){
            $this->error("Server API Key not set");
        }

        $fields = array(
            'registration_ids'  => $this->devices,
            'data'              => array( "message" => $message ),
        );

        if(is_array($data)){
            foreach ($data as $key => $value) {
                $fields['data'][$key] = $value;
            }
        }
        $headers = array(
            'Authorization: key=' . $this->serverApiKey,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt( $ch, CURLOPT_URL, $this->url );

        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

        // Avoids problem with https certificate
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);

        return $result;
    }

    /**
     * Make a new GCM object and send it.
     *
     * @param array $devices Array of device ids
     * @param string $message the message to send
     * @param array $data Array of data to accompany the message
     *
     * @return bool
     */
    public static function make($devices, $message, $data)
    {
        $gcm = new GCM();
        $gcm->setDevices($devices);
        $response = $gcm->send($message, $data);

        Log::info(json_encode($response));

        return $response;
    }

    public function error($msg)
    {
        echo "Android send notification failed with error:";
        echo "\t" . $msg;
        exit(1);
    }
}
