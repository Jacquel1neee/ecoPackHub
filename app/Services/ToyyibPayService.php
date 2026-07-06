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
        $returnUrl = (string) config('toyyibpay.return_url', '');;
        $callbackUrl = (string) config('toyyibpay.callback_url', '');

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
            'billEmail' => '',
            'billPhone' => $order->phone ?? '',
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
            $response = Http::asForm()->post(config('toyyibpay.endpoint', 'https://toyyibpay.com/index.php/api/createBill'), $payload);

            if ($response->successful()) {
                $body = $response->json();
                $billCode = $this->extractBillCode($body);

                if ($billCode) {
                    return [
                        'success' => true,
                        'redirect_url' => $this->buildPaymentUrl($billCode),
                        'mock' => false,
                        'mode' => 'real',
                        'payload' => $payload,
                        'bill_code' => $billCode,
                    ];
                }
            }

            return [
                'success' => false,
                'mode' => 'real',
                'message' => 'ToyyibPay bill creation failed. Please verify your credentials and endpoint.',
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

    protected function buildPaymentUrl(string $billCode): string
    {
        return rtrim(config('toyyibpay.payment_url_base', 'https://toyyibpay.com/'), '/') . '/' . ltrim($billCode, '/');
    }
}
