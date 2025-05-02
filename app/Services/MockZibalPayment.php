<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MockZibalPayment extends ZibalPayment
{
    /**
     * Create a mock payment request
     *
     * @param int $amount Amount in IRR (Rials)
     * @param string $callbackUrl Custom callback URL
     * @param string $orderId Order ID or reference number
     * @param string|null $description Payment description
     * @param array|null $metadata Additional metadata
     * @return array|null Response simulating Zibal or null on failure
     */
    public function request($amount, $callbackUrl, $orderId, $description = null, $metadata = null)
    {
        // Prepare data (same as real implementation for logging)
        $data = [
            'merchant' => $this->merchant,
            'amount' => $amount,
            'callbackUrl' => $callbackUrl ?? url($this->callbackUrl),
        ];

        // Add optional parameters
        if ($orderId) {
            $data['orderId'] = $orderId;
        }

        if ($description) {
            $data['description'] = $this->description_prefix . ' ' . $description;
        }

        if ($metadata) {
            $data['metadata'] = $metadata;
        }

        $this->logInfo('Payment request initiated (MOCK)', $data);
        
        // Simulate success response from Zibal
        $mockResponse = [
            'trackId' => Str::random(10),
            'result' => 100,
            'message' => 'success',
            'orderId' => $orderId,
        ];
        
        $this->logInfo('Payment request response (MOCK)', $mockResponse);
        
        return $mockResponse;
    }

    /**
     * Get the payment URL for redirecting customer (mocked)
     *
     * @param string $trackId The trackId from request response
     * @return string Payment gateway URL
     */
    public function getPaymentUrl(string $trackId)
    {
        // In a real mock scenario, this could point to a local route that simulates the payment gateway
        return url('/mock-payment/' . $trackId);
    }

    /**
     * Verify a payment after callback (mocked)
     *
     * @param string $trackId The trackId from request response
     * @return array|null Response simulating Zibal or null on failure
     */
    public function verify($trackId)
    {
        $data = [
            'merchant' => $this->merchant,
            'trackId' => $trackId,
        ];

        $this->logInfo('Payment verification initiated (MOCK)', $data);
        
        // Simulate successful verification
        $mockResponse = [
            'result' => 100,
            'message' => 'success',
            'trackId' => $trackId,
            'orderId' => 'mock-order-' . Str::random(5),
            'amount' => 10000,
            'status' => 2, // Paid and verified
            'refNumber' => 'mock-ref-' . rand(1000000, 9999999),
            'cardNumber' => '6104****1234',
            'cardHash' => 'mock-hash-' . Str::random(20),
        ];
        
        $this->logInfo('Payment verification response (MOCK)', $mockResponse);
        
        return $mockResponse;
    }

    /**
     * Log info message if logging is enabled
     *
     * @param string $message Message to log
     * @param array $context Context data
     */
    protected function logInfo(string $message, array $context = [])
    {
        // Call parent method to maintain consistent logging
        parent::logInfo($message, $context);
    }

    /**
     * Log error message if logging is enabled
     *
     * @param string $message Message to log
     * @param array $context Context data
     */
    protected function logError(string $message, array $context = [])
    {
        // Call parent method to maintain consistent logging
        parent::logError($message, $context);
    }
} 