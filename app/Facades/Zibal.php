<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array|null request(int $amount, string $callbackUrl = null, string $orderId = null, string $description = null, array $metadata = [])
 * @method static string getPaymentUrl(string $trackId)
 * @method static array|null verify(string $trackId)
 * @method static string getResultMessage(int $code)
 * 
 * @see \App\Services\ZibalPayment
 */
class Zibal extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'zibal';
    }
} 