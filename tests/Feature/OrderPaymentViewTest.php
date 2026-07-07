<?php

namespace Tests\Feature;

use App\Http\Controllers\OrderController;
use App\Models\Order;
use App\Services\ToyyibPayService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderPaymentViewTest extends TestCase
{
    #[Test]
    public function it_renders_a_pay_now_button_that_targets_the_payment_route(): void
    {
        $order = new Order([
            'order_number' => 'ECO-TEST29',
            'total_amount' => 100.00,
            'payment_status' => 'pending',
            'status' => 'pending',
            'shipping_address' => 'Test address',
            'phone' => '0123456789',
        ]);
        $order->id = 29;
        $order->setRelation('items', collect());
        $order->created_at = now();

        $html = view('order.show', ['order' => $order])->render();

        $this->assertStringContainsString('/orders/29/pay', $html);
    }

    #[Test]
    public function it_redirects_to_toyyibpay_when_the_payment_flow_is_started(): void
    {
        config()->set('toyyibpay.enabled', true);
        config()->set('toyyibpay.mode', 'real');
        config()->set('toyyibpay.secret_key', 'test-secret');
        config()->set('toyyibpay.category_code', 'test-category');
        config()->set('toyyibpay.endpoint', 'https://example.test/createBill');
        config()->set('toyyibpay.payment_url_base', 'https://toyyibpay.com');

        Http::fake([
            'https://example.test/createBill' => Http::response([['BillCode' => 'ABC123']], 200),
        ]);

        $user = new class implements Authenticatable {
            public int $id = 1;
            public int $role = 0;
            public string $name = 'Test User';

            public function getAuthIdentifierName()
            {
                return 'id';
            }

            public function getAuthIdentifier()
            {
                return $this->id;
            }

            public function getAuthPassword()
            {
                return 'secret';
            }

            public function getRememberToken()
            {
                return null;
            }

            public function setRememberToken($value)
            {
            }

            public function getRememberTokenName()
            {
                return 'remember_token';
            }

            public function getAuthPasswordName()
            {
                return 'password';
            }
        };
        $this->actingAs($user);

        $order = new Order([
            'user_id' => $user->id,
            'order_number' => 'ECO-REAL-1',
            'total_amount' => 120.50,
            'status' => 'pending',
            'payment_status' => 'pending',
            'delivery_method' => 'shipping',
            'shipping_address' => 'Test address',
            'phone' => '0123456789',
        ]);

        $order->id = 77;

        $controller = new OrderController();
        $response = $controller->pay($order, new ToyyibPayService());

        $this->assertSame('https://toyyibpay.com/ABC123', $response->getTargetUrl());
    }
}
