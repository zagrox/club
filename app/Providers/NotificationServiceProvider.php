<?php

namespace App\Providers;

use App\Models\DatabaseNotification;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\NotificationServiceProvider as BaseNotificationServiceProvider;

class NotificationServiceProvider extends BaseNotificationServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Call parent registration first
        parent::register();

        // Override the DatabaseNotification model binding
        $this->app->bind(
            \Illuminate\Notifications\DatabaseNotification::class,
            DatabaseNotification::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Set the notification table name
        config(['database.notifications.table' => 'notifications']);
    }
}