<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    protected $baseUrl;
    protected $cpsBaseUrl;
    protected $clientId;
    protected $secretKey;
    protected $isLive;

    public function __construct()
    {
        $this->baseUrl = config('services.rexpay.base_url', env('GATEWAY_BASE_URL', 'https://pgs-sandbox.globalaccelerex.com'));
        $this->isLive = strpos($this->baseUrl, 'sandbox') === false;
        $this->cpsBaseUrl = $this->isLive ? 'https://cps.globalaccelerex.com' : 'https://pgs-sandbox.globalaccelerex.com';
        $this->clientId = config('services.rexpay.client_id', env('GATEWAY_CLIENT_ID'));
        $this->secretKey = config('services.rexpay.secret_key', env('GATEWAY_SECRET_KEY'));
    }

    public function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->secretKey);
    }

    public function createPayment($reference, $email, $amount, $callbackUrl)
    {
        try {
            $response = Http::withBasicAuth($this->clientId, $this->secretKey)
                ->post(rtrim($this->baseUrl, '/') . '/api/pgs/payment/v2/createPayment', [
                    'reference' => $reference,
                    'customerReference' => $reference,
                    'userId' => $email,
                    'amount' => $amount,
                    'currency' => 'NGN',
                    'email' => $email,
                    'callbackUrl' => $callbackUrl,
                    'callback_url' => $callbackUrl,
                    'returnUrl' => $callbackUrl,
                    'return_url' => $callbackUrl,
                    'redirectUrl' => $callbackUrl,
                    'redirect_url' => $callbackUrl,
                ]);

            $json = $response->json();
            
            if (!$response->successful()) {
                Log::error('RexPay Init Error (HTTP ' . $response->status() . '):', $json ?? []);
                return ['success' => false, 'error' => 'Gateway error', 'data' => $json];
            }

            return ['success' => true, 'data' => $json];
        } catch (\Exception $e) {
            Log::error('RexPay Init Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Connection to payment gateway failed'];
        }
    }

    public function verifyPayment($reference)
    {
        $endpointsToTry = [
            ['method' => 'post', 'url' => '/api/cps/v1/getTransactionStatus', 'payload' => ['transactionReference' => $reference]],
            ['method' => 'post', 'url' => '/api/cps/v1/getTransactionStatus', 'payload' => ['reference' => $reference]],
            ['method' => 'post', 'url' => '/api/pgs/payment/v1/getTransactionStatus', 'payload' => ['transactionReference' => $reference]],
            ['method' => 'post', 'url' => '/api/pgs/payment/v1/getTransactionStatus', 'payload' => ['reference' => $reference]],
            ['method' => 'post', 'url' => '/api/pgs/payment/v2/getTransactionStatus', 'payload' => ['transactionReference' => $reference]],
            ['method' => 'post', 'url' => '/api/pgs/payment/v2/getTransactionStatus', 'payload' => ['reference' => $reference]],
            ['method' => 'get', 'url' => '/api/pgs/payment/v1/getPayments/?reference=' . $reference, 'payload' => []]
        ];

        $verifyResponse = null;
        $debugLogs = [];
        $workingUrl = '';

        foreach ($endpointsToTry as $endpoint) {
            $base = strpos($endpoint['url'], '/api/cps') === 0 ? $this->cpsBaseUrl : $this->baseUrl;
            $fullUrl = rtrim($base, '/') . $endpoint['url'];
            
            try {
                if ($endpoint['method'] === 'post') {
                    $response = Http::withBasicAuth($this->clientId, $this->secretKey)->post($fullUrl, $endpoint['payload']);
                } else {
                    $response = Http::withBasicAuth($this->clientId, $this->secretKey)->get($fullUrl);
                }
                
                $json = $response->json();
                $debugLogs[$endpoint['url']] = $json;

                if (is_array($json) && (!isset($json['responseMessage']) || strpos($json['responseMessage'], 'No static resource') === false)) {
                    if (strpos($endpoint['url'], 'getPayments') !== false && isset($json['data']) && is_array($json['data'])) {
                        $found = collect($json['data'])->firstWhere('reference', $reference);
                        $verifyResponse = $found ? ['data' => $found] : $json;
                    } else {
                        $verifyResponse = $json;
                    }
                    $workingUrl = $endpoint['url'];
                    break;
                }
            } catch (\Exception $e) {
                Log::error("RexPay verify error on {$fullUrl}: " . $e->getMessage());
            }
        }

        Log::info('RexPay checkStatus verify response for reference: ' . $reference, [
            'workingUrl' => $workingUrl, 
            'response' => $verifyResponse, 
            'all_attempts' => $debugLogs
        ]);

        return ['success' => $verifyResponse !== null, 'data' => $verifyResponse, 'debugLogs' => $debugLogs];
    }
}
