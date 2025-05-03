<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;

class WalletNotification extends BaseDatabaseNotification
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wallet_notifications';
} 