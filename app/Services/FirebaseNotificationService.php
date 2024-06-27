<?php

namespace App\Services;

use GuzzleHttp\Client;

class FirebaseNotificationService
{
    protected $client;
    protected $serverKey;
    protected $senderId;

    public function __construct()
    {
        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://fcm.googleapis.com',
        ]);
        $this->serverKey = env('FCM_SERVER_KEY');
        $this->senderId = env('FCM_SENDER_ID');
    }

    public function sendNotification($to, $notification, $data = [])
    {
        $response = $this->client->post('/fcm/send', [
            'headers' => [
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'to' => $to,
                'notification' => $notification,
                'data' => $data,
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}
