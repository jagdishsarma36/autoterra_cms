<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    protected string $keyId;
    protected string $keySecret;
    protected string $apiBase;

    public function __construct()
    {
        $this->keyId = config('razorpay.key_id');
        $this->keySecret = config('razorpay.key_secret');
        $this->apiBase = config('razorpay.api_base', 'https://api.razorpay.com/v1');
    }

    /**
     * Make an authenticated request to Razorpay API.
     */
    protected function request(string $method, string $path, array $body = []): array
    {
        $url = $this->apiBase . $path;

        $response = Http::withBasicAuth($this->keyId, $this->keySecret)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->send(strtoupper($method), $url, $body ? ['json' => $body] : []);

        $data = $response->json();

        if ($response->failed()) {
            $message = $data['error']['description'] ?? $data['error'] ?? 'Unknown Razorpay error';
            Log::error('[Razorpay] API error', ['path' => $path, 'status' => $response->status(), 'error' => $message]);
            return ['error' => $message, 'http_code' => $response->status()];
        }

        return $data ?? [];
    }

    /**
     * Create a Razorpay Order for one-time payment.
     */
    public function createOrder(int $amountInPaise, string $currency = 'INR', array $notes = []): array
    {
        $receipt = 'rcpt_' . substr(md5(uniqid('', true)), 0, 12);

        return $this->request('POST', '/orders', [
            'amount' => $amountInPaise,
            'currency' => $currency,
            'receipt' => $receipt,
            'payment_capture' => 1,
            'notes' => $notes,
        ]);
    }

    /**
     * Verify a payment signature.
     */
    public function verifyPaymentSignature(array $params): bool
    {
        $orderId = $params['razorpay_order_id'] ?? '';
        $paymentId = $params['razorpay_payment_id'] ?? '';
        $signature = $params['razorpay_signature'] ?? '';
        $isSubscription = !empty($params['is_subscription']);

        if (empty($paymentId) || empty($signature)) {
            return false;
        }

        if ($isSubscription) {
            $subscriptionId = $params['razorpay_subscription_id'] ?? '';
            if (empty($subscriptionId)) return false;
            $payload = $paymentId . '|' . $subscriptionId;
        } else {
            if (empty($orderId)) return false;
            $payload = $orderId . '|' . $paymentId;
        }

        $expected = hash_hmac('sha256', $payload, $this->keySecret);
        return hash_equals($expected, $signature);
    }

    /**
     * Create a Razorpay Plan for subscriptions.
     */
    public function createPlan(int $amountInPaise, string $currency, string $period, int $interval, string $name, string $description = ''): array
    {
        return $this->request('POST', '/plans', [
            'period' => $period,
            'interval' => $interval,
            'item' => [
                'name' => $name,
                'amount' => $amountInPaise,
                'currency' => $currency,
                'description' => $description,
            ],
        ]);
    }

    /**
     * Create a Razorpay Subscription.
     */
    public function createSubscription(string $planId, int $totalCount = 12): array
    {
        return $this->request('POST', '/subscriptions', [
            'plan_id' => $planId,
            'total_count' => $totalCount,
            'quantity' => 1,
            'customer_notify' => 1,
        ]);
    }

    /**
     * Fetch a payment by ID.
     */
    public function fetchPayment(string $paymentId): array
    {
        return $this->request('GET', "/payments/{$paymentId}");
    }

    /**
     * Fetch a subscription.
     */
    public function fetchSubscription(string $subscriptionId): array
    {
        return $this->request('GET', "/subscriptions/{$subscriptionId}");
    }

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(string $subscriptionId): array
    {
        return $this->request('POST', "/subscriptions/{$subscriptionId}/cancel", [
            'cancel_at_cycle_end' => 1,
        ]);
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(string $rawBody, string $signature): bool
    {
        $webhookSecret = config('razorpay.webhook_secret');
        $expected = hash_hmac('sha256', $rawBody, $webhookSecret);
        return hash_equals($expected, $signature);
    }

    /**
     * Get the Razorpay Key ID (for frontend).
     */
    public function getKeyId(): string
    {
        return $this->keyId;
    }
}
