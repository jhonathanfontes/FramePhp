<?php

class PushNotificationService
{
    public function sendPushNotification($deviceToken, $title, $body)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = [
            'to' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body' => $body
            ]
        ];
        $headers = [
            'Authorization: key=' . getenv('FCM_SERVER_KEY'),
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        echo $result;
    }
}