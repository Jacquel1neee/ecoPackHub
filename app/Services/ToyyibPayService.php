<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ToyyibPayService
{
    public function buildBillPayload(Order $order): array
    {
        $amountInSen = (int) round($order->total_amount * 100);
        $returnUrl = (string) config('toyyibpay.return_url', '');
        $callbackUrl = (string) config('toyyibpay.callback_url', '');
        $customerName = trim((string) ($order->getAttribute('customer_name') ?: 'EcoPackHub Customer'));
        $customerEmail = trim((string) ($order->getAttribute('email') ?: config('mail.from.address', 'noreply@example.com')));
        $customerPhone = trim((string) ($order->phone ?? ''));

        $returnUrl = str_replace('{order}', (string) $order->id, $returnUrl);
        $callbackUrl = str_replace('{order}', (string) $order->id, $callbackUrl);

        return [
            'userSecretKey' => config('toyyibpay.secret_key'),
            'categoryCode' => config('toyyibpay.category_code'),
            'billName' => 'EcoPackHub Order ' . $order->order_number,
            'billDescription' => 'Payment for order ' . $order->order_number,
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => (string) $amountInSen,
            'billExternalReferenceNo' => $order->order_number,
            'billReturnUrl' => $returnUrl,
            'billCallbackUrl' => $callbackUrl,
            'billTo' => $customerName,
            'billEmail' => $customerEmail,
            'billPhone' => $customerPhone,
            'mode' => config('toyyibpay.mode', 'test'),
        ];
    }

    public function createBill(Order $order): array
    {
        $payload = $this->buildBillPayload($order);

        if (! config('toyyibpay.enabled', false)) {
            return [
                'success' => false,
                'redirect_url' => route('orders.show', ['order' => $order->id]),
                'mock' => false,
                'mode' => 'real',
                'message' => 'ToyyibPay is disabled in the configuration.',
                'payload' => $payload,
            ];
        }

        if (config('toyyibpay.mode', 'real') === 'mock') {
            return [
                'success' => true,
                'redirect_url' => route('orders.show', ['order' => $order->id]),
                'mock' => true,
                'mode' => 'mock',
                'message' => 'ToyyibPay is running in mock mode for local testing.',
                'payload' => $payload,
            ];
        }

        if (empty($payload['userSecretKey']) || empty($payload['categoryCode'])) {
            return [
                'success' => false,
                'redirect_url' => route('orders.show', ['order' => $order->id]),
                'mock' => false,
                'mode' => 'real',
                'message' => 'ToyyibPay secret key and category code are required in real mode.',
                'payload' => $payload,
            ];
        }

        try {
            $requestPayload = $payload;
            unset($requestPayload['mode']);

            $endpoint = $this->resolveEndpoint();
            $response = $this->sendCreateBillRequest($endpoint, $requestPayload);

            if ($response->successful()) {
                $body = $response->json();

                if (! is_array($body) && ! is_object($body)) {
                    $decoded = json_decode($response->body(), true);
                    $body = is_array($decoded) ? $decoded : $response->body();
                }

                $billCode = $this->extractBillCode($body);

                if ($billCode) {
                    return [
                        'success' => true,
                        'redirect_url' => $this->buildPaymentUrl($billCode, $endpoint),
                        'mock' => false,
                        'mode' => 'real',
                        'payload' => $payload,
                        'bill_code' => $billCode,
                    ];
                }
            }

            if ($this->shouldRetryWithAlternateEndpoint($response->body())) {
                $alternateEndpoint = $this->resolveAlternateEndpoint($endpoint);
                if ($alternateEndpoint) {
                    $alternateResponse = $this->sendCreateBillRequest($alternateEndpoint, $requestPayload);

                    if ($alternateResponse->successful()) {
                        $alternateBody = $alternateResponse->json();

                        if (! is_array($alternateBody) && ! is_object($alternateBody)) {
                            $decoded = json_decode($alternateResponse->body(), true);
                            $alternateBody = is_array($decoded) ? $decoded : $alternateResponse->body();
                        }

                        $alternateBillCode = $this->extractBillCode($alternateBody);
                        if ($alternateBillCode) {
                            return [
                                'success' => true,
                                'redirect_url' => $this->buildPaymentUrl($alternateBillCode, $alternateEndpoint),
                                'mock' => false,
                                'mode' => 'real',
                                'payload' => $payload,
                                'bill_code' => $alternateBillCode,
                                'endpoint_fallback_used' => true,
                            ];
                        }
                    }
                }
            }

            $gatewayMessage = $this->extractGatewayMessage($response->body());

            return [
                'success' => false,
                'mode' => 'real',
                'message' => $gatewayMessage ?: 'ToyyibPay bill creation failed. Please verify your credentials and endpoint.',
                'payload' => $payload,
                'response' => $response->body(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'mode' => 'real',
                'message' => 'ToyyibPay request could not be completed: ' . $e->getMessage(),
                'payload' => $payload,
            ];
        }
    }

    protected function extractBillCode(mixed $body): ?string
    {
        if (is_string($body)) {
            $parsed = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $this->extractBillCode($parsed);
            }

            if (preg_match('/BillCode\s*[:=]\s*"?([A-Za-z0-9_-]+)"?/i', $body, $matches)) {
                return $matches[1];
            }
        }

        if (is_array($body)) {
            foreach ($body as $item) {
                if (is_array($item) && ! empty($item['BillCode'])) {
                    return (string) $item['BillCode'];
                }

                if (is_object($item) && isset($item->BillCode)) {
                    return (string) $item->BillCode;
                }
            }
        }

        if (is_object($body) && isset($body->BillCode)) {
            return (string) $body->BillCode;
        }

        if (is_array($body) && isset($body['BillCode'])) {
            return (string) $body['BillCode'];
        }

        return null;
    }

    protected function resolveEndpoint(): string
    {
        $configuredEndpoint = trim((string) config('toyyibpay.endpoint', ''));
        if ($configuredEndpoint !== '') {
            return $configuredEndpoint;
        }

        $mode = strtolower((string) config('toyyibpay.mode', 'test'));

        return $mode === 'real'
            ? 'https://toyyibpay.com/index.php/api/createBill'
            : 'https://dev.toyyibpay.com/index.php/api/createBill';
    }

    protected function resolveAlternateEndpoint(string $currentEndpoint): ?string
    {
        $normalized = strtolower(trim($currentEndpoint));

        if (str_contains($normalized, 'dev.toyyibpay.com')) {
            return 'https://toyyibpay.com/index.php/api/createBill';
        }

        if (str_contains($normalized, 'toyyibpay.com')) {
            return 'https://dev.toyyibpay.com/index.php/api/createBill';
        }

        return null;
    }

    protected function shouldRetryWithAlternateEndpoint(string $responseBody): bool
    {
        if (! config('toyyibpay.auto_endpoint_fallback', true)) {
            return false;
        }

        if (trim((string) config('toyyibpay.endpoint', '')) !== '') {
            return false;
        }

        return str_contains(strtoupper($responseBody), 'KEY-DID-NOT-EXIST');
    }

    protected function sendCreateBillRequest(string $endpoint, array $payload)
    {
        return Http::asForm()
            ->acceptJson()
            ->timeout(20)
            ->post($endpoint, $payload);
    }

    protected function extractGatewayMessage(string $body): ?string
    {
        $body = trim($body);

        if ($body === '') {
            return null;
        }

        if (preg_match('/\[([^\]]+)\]/', $body, $matches)) {
            $code = trim($matches[1]);
            if ($code !== '') {
                return 'ToyyibPay bill creation failed: ' . $code;
            }
        }

        $decoded = json_decode($body, true);
        if (is_array($decoded)) {
            foreach ($decoded as $item) {
                if (is_array($item)) {
                    $message = $item['msg'] ?? $item['message'] ?? $item['status'] ?? null;
                    if (is_string($message) && trim($message) !== '') {
                        return 'ToyyibPay bill creation failed: ' . trim($message);
                    }
                }
            }
        }

        return null;
    }

    protected function buildPaymentUrl(string $billCode, ?string $endpoint = null): string
    {
        $base = trim((string) config('toyyibpay.payment_url_base', ''));

        if ($base === '') {
            $endpoint = strtolower((string) $endpoint);
            if ($endpoint !== '' && str_contains($endpoint, 'dev.toyyibpay.com')) {
                $base = 'https://dev.toyyibpay.com';
            } else {
                $mode = strtolower((string) config('toyyibpay.mode', 'test'));
                $base = $mode === 'real'
                    ? 'https://toyyibpay.com'
                    : 'https://dev.toyyibpay.com';
            }
        }

        return rtrim($base, '/') . '/' . ltrim($billCode, '/');
    }
}
