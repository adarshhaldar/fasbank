<?php

namespace App\Helpers;

use App\Models\Notification;

trait NotificationHelper
{
    public function saveNotificaiton($user, $slug, $title, $body, $dataId = null, $url = null)
    {
        Notification::create([
            'user_id' => $user->id,
            'slug' => $slug,
            'title' => $title,
            'body' => $body,
            'data_id' => $dataId,
            'url' => $url
        ]);
    }

    public function sendPayNotification($toUser, $user, $amount)
    {
        $slug = 'transfer';
        $title = 'Payment Received';
        $body = $user->name . ' has paid you ' . $amount;
        $dataId = null;
        $url = env('SERVE_APP_URL') . 'recent-payment/'.$user->bank->fbid;

        $this->saveNotificaiton($toUser, $slug, $title, $body, $user->bank->fbid, $url);
    }

    public function sendRequestNotification($toUser, $user, $amount)
    {
        $slug = 'request';
        $title = 'Payment Requested';
        $body = $user->name . ' has requested you to pay ' . $amount;
        $dataId = null;
        $url = env('SERVE_APP_URL') . 'recent-payment/'.$user->bank->fbid;

        $this->saveNotificaiton($toUser, $slug, $title, $body, $user->bank->fbid, $url);
    }
}
