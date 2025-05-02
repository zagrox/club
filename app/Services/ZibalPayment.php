<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZibalPayment
{
    protected $merchant;
    protected $apiUrls;
    protected $callbackUrl;
    protected $sandbox;
    protected $mock;
    protected $logEnabled;
    protected $logChannel;
    protected $currency;
    protected $description_prefix;

    /**
     * Create a new Zibal Payment instance.
     */
    public function __construct()
    {
        $this->merchant = config('zibal.merchant');
        $this->apiUrls = config('zibal.api_url');
        $this->callbackUrl = config('zibal.callback_url');
        $this->sandbox = config('zibal.sandbox');
        $this->mock = config('zibal.mock');
        $this->logEnabled = config('zibal.log_enabled');
        $this->logChannel = config('zibal.log_channel');
        $this->currency = config('zibal.currency');
        $this->description_prefix = config('zibal.description_prefix');
    }

    /**
     * Request a new payment from Zibal.
     *
     * @param int $amount Amount in IRR (Rials)
     * @param string $callbackUrl URL to redirect after payment
     * @param string $orderId Your system's order ID
     * @param string|null $description Payment description
     * @param array|null $metadata Additional data for payment
     * @return array|null Response from Zibal or null on failure
     */
    public function request($amount, $callbackUrl, $orderId, $description = null, $metadata = null)
    {
        $data = [
            'merchant' => $this->merchant,
            'amount' => $amount,
            'callbackUrl' => $callbackUrl,
            'orderId' => $orderId,
        ];

        if ($description) {
            $data['description'] = $this->description_prefix . ' ' . $description;
        }

        if ($metadata) {
            $data['metadata'] = $metadata;
        }

        if ($this->sandbox) {
            $data['bankGateway'] = 'test';
        }

        if ($this->mock) {
            $data['bankGateway'] = 'mock';
        }

        Log::info('Payment request initiated', $data);

        try {
            $response = $this->makeRequest('request', $data);
            
            if (isset($response['result']) && ($response['result'] == 100 || $response['result'] == 201)) {
                Log::info('Payment request successful', [
                    'trackId' => $response['trackId'] ?? 'N/A',
                    'result' => $response['result']
                ]);
                return $response;
            } else {
                Log::warning('Payment request failed', [
                    'result' => $response['result'] ?? 'unknown',
                    'message' => isset($response['result']) ? $this->getResultMessage($response['result']) : 'Unknown error'
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Payment request exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get the payment URL for redirecting customer
     *
     * @param string $trackId The trackId from request response
     * @return string Payment gateway URL
     */
    public function getPaymentUrl(string $trackId)
    {
        return $this->apiUrls['start_pay'] . $trackId;
    }

    /**
     * Verify transaction result.
     *
     * @param string $trackId
     * @return array|null
     */
    public function verify($trackId)
    {
        try {
            Log::info('Verifying Zibal payment', ['trackId' => $trackId]);
            
            $response = $this->makeRequest('verify', [
                'merchant' => $this->merchant,
                'trackId' => $trackId,
            ]);

            Log::info('Zibal verify response', ['response' => $response]);
            
            if ($response && isset($response['result'])) {
                // Result code 100 means success
                if ($response['result'] == 100) {
                    Log::info('Zibal payment verified successfully', [
                        'trackId' => $trackId, 
                        'refNumber' => $response['refNumber'] ?? 'N/A'
                    ]);
                } else {
                    Log::warning('Zibal payment verification failed', [
                        'trackId' => $trackId, 
                        'resultCode' => $response['result'],
                        'message' => $this->getResultMessage($response['result'])
                    ]);
                }
            }
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Zibal verify exception', [
                'trackId' => $trackId, 
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Get the status message for a result code
     *
     * @param int $code Result code from Zibal
     * @return string Status message
     */
    public function getResultMessage(int $code)
    {
        $messages = [
            100 => 'با موفقیت انجام شد',
            102 => 'merchant یافت نشد',
            103 => 'merchant غیرفعال',
            104 => 'merchant نامعتبر',
            201 => 'قبلا تایید شده',
            105 => 'amount بایستی بزرگتر از 1,000 ریال باشد',
            106 => 'callbackUrl نامعتبر می‌باشد (شروع با http و https)',
            113 => 'amount مبلغ تراکنش از سقف میزان تراکنش بیشتر است',
            201 => 'تکرار پرداخت موفق',
            202 => 'تراکنش یافت نشد',
            203 => 'trackId نامعتبر می‌باشد',
            1 => 'در انتظار پردخت',
            2 => 'پرداخت شده - تاییدشده',
            3 => 'پرداخت شده - تاییدنشده',
            4 => 'باطل شده',
            5 => 'برگشت به پرداخت کننده',
            6 => 'برگشت خورده سیستمی',
            7 => 'انصراف از پرداخت',
            8 => 'به درگاه پرداخت منتقل شد',
            10 => 'در انتظار تایید پرداخت',
            11 => 'کاربر مسدود شده است',
            12 => 'API Key یافت نشد',
            13 => 'درخواست شما از {ip} ارسال شده است. این IP با IP های ثبت شده در وب سرویس همخوانی ندارد',
            14 => 'وب سرویس شما در حال بررسی است و یا تایید نشده است'
        ];

        return $messages[$code] ?? 'کد نامشخص';
    }

    /**
     * Log info message if logging is enabled
     *
     * @param string $message Message to log
     * @param array $context Context data
     */
    protected function logInfo(string $message, array $context = [])
    {
        if ($this->logEnabled) {
            Log::channel($this->logChannel)->info('[Zibal] ' . $message, $context);
        }
    }

    /**
     * Log error message if logging is enabled
     *
     * @param string $message Message to log
     * @param array $context Context data
     */
    protected function logError(string $message, array $context = [])
    {
        if ($this->logEnabled) {
            Log::channel($this->logChannel)->error('[Zibal] ' . $message, $context);
        }
    }

    /**
     * Make a request to Zibal API.
     *
     * @param string $endpoint
     * @param array $data
     * @return array|null
     */
    protected function makeRequest($endpoint, array $data)
    {
        try {
            if (!isset($this->apiUrls[$endpoint])) {
                Log::error('Invalid Zibal API endpoint', ['endpoint' => $endpoint]);
                return null;
            }
            
            $url = $this->apiUrls[$endpoint];
            Log::info('Making Zibal API request', [
                'endpoint' => $endpoint,
                'url' => $url,
                'data' => $data
            ]);
            
            $response = Http::post($url, $data);
            $result = $response->json();
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Zibal API request failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
} 