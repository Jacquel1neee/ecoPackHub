<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Services\ToyyibPayService;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ToyyibPayServiceTest extends TestCase
{
    #[Test]
    public function it_builds_a_test_mode_bill_payload_for_an_order(): void
    {
        config([
            'toyyibpay.enabled' => true,
            'toyyibpay.mode' => 'test',
            'toyyibpay.secret_key' => 'test-secret',
            'toyyibpay.category_code' => 'test-category',
            'toyyibpay.return_url' => 'https://example.com/payment/return',
            'toyyibpay.callback_url' => 'https://example.com/payment/callback',
        ]);

        $order = new Order([
            'order_number' => 'ECO-TEST123',
            'total_amount' => 125.50,
            'user_id' => 1,
            'shipping_address' => '123 Jalan Test',
            'phone' => '0123456789',
        ]);

        $service = new ToyyibPayService();
        $payload = $service->buildBillPayload($order);

        $this->assertSame('test-secret', $payload['userSecretKey']);
        $this->assertSame('test-category', $payload['categoryCode']);
        $this->assertSame('ECO-TEST123', $payload['billExternalReferenceNo']);
        $this->assertSame('12550', $payload['billAmount']);
        $this->assertSame('https://example.com/payment/return', $payload['billReturnUrl']);
        $this->assertSame('https://example.com/payment/callback', $payload['billCallbackUrl']);
        $this->assertSame('test', $payload['mode']);
    }

    #[Test]
    public function it_creates_a_payment_redirect_from_a_toyyibpay_bill_response(): void
    {
        Http::fake([
            'https://toyyibpay.com/index.php/api/createBill' => Http::response([
                ['BillCode' => 'ABC123'],
            ], 200),
        ]);

        config([
            'toyyibpay.enabled' => true,
            'toyyibpay.mode' => 'real',
            'toyyibpay.secret_key' => 'test-secret',
            'toyyibpay.category_code' => 'test-category',
            'toyyibpay.return_url' => 'https://example.com/payment/return',
            'toyyibpay.callback_url' => 'https://example.com/payment/callback',
        ]);

        $order = new Order([
            'id' => 12,
            'order_number' => 'ECO-TEST456',
            'total_amount' => 50.00,
            'user_id' => 1,
            'phone' => '0123456789',
        ]);

        $service = new ToyyibPayService();
        $result = $service->createBill($order);

        $this->assertTrue($result['success']);
        $this->assertSame('https://toyyibpay.com/ABC123', $result['redirect_url']);
        $this->assertSame('ABC123', $result['bill_code']);
    }

    #[Test]
    public function it_surfaces_a_gateway_error_code_when_toyyibpay_returns_bracketed_text(): void
    {
        Http::fake([
            'https://toyyibpay.com/index.php/api/createBill' => Http::response('[KEY-DID-NOT-EXIST]', 200),
        ]);

        config([
            'toyyibpay.enabled' => true,
            'toyyibpay.mode' => 'real',
            'toyyibpay.secret_key' => 'test-secret',
            'toyyibpay.category_code' => 'test-category',
            'toyyibpay.return_url' => 'https://example.com/payment/return',
            'toyyibpay.callback_url' => 'https://example.com/payment/callback',
            'toyyibpay.endpoint' => 'https://toyyibpay.com/index.php/api/createBill',
        ]);

        $order = new Order([
            'id' => 12,
            'order_number' => 'ECO-TEST789',
            'total_amount' => 50.00,
            'user_id' => 1,
            'phone' => '0123456789',
        ]);

        $service = new ToyyibPayService();
        $result = $service->createBill($order);

        $this->assertFalse($result['success']);
        $this->assertSame('ToyyibPay bill creation failed: KEY-DID-NOT-EXIST', $result['message']);
    }

    #[Test]
    public function it_retries_with_alternate_endpoint_when_key_is_not_found_and_uses_fallback_response(): void
    {
        Http::fake([
            'https://toyyibpay.com/index.php/api/createBill' => Http::response('[KEY-DID-NOT-EXIST]', 200),
            'https://dev.toyyibpay.com/index.php/api/createBill' => Http::response([
                ['BillCode' => 'DEV123'],
            ], 200),
        ]);

        config([
            'toyyibpay.enabled' => true,
            'toyyibpay.mode' => 'real',
            'toyyibpay.secret_key' => 'test-secret',
            'toyyibpay.category_code' => 'test-category',
            'toyyibpay.return_url' => 'https://example.com/payment/return',
            'toyyibpay.callback_url' => 'https://example.com/payment/callback',
            'toyyibpay.endpoint' => '',
            'toyyibpay.auto_endpoint_fallback' => true,
            'toyyibpay.payment_url_base' => '',
        ]);

        $order = new Order([
            'id' => 12,
            'order_number' => 'ECO-TEST999',
            'total_amount' => 50.00,
            'user_id' => 1,
            'phone' => '0123456789',
        ]);

        $service = new ToyyibPayService();
        $result = $service->createBill($order);

        $this->assertTrue($result['success']);
        $this->assertSame('DEV123', $result['bill_code']);
        $this->assertSame('https://dev.toyyibpay.com/DEV123', $result['redirect_url']);
        $this->assertTrue($result['endpoint_fallback_used']);
    }
}
