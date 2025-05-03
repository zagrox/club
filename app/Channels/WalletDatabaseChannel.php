<?php

namespace App\Channels;

use App\Models\WalletNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class WalletDatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return \App\Models\WalletNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toDatabase($notifiable);

        return $notifiable->walletNotifications()->create([
            'id' => Str::uuid()->toString(),
            'type' => get_class($notification),
            'data' => $data,
            'read_at' => null,
        ]);
    }
}