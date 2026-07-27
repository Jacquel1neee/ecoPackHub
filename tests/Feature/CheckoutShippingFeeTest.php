<?php

namespace Tests\Feature;

use App\Http\Controllers\OrderController;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckoutShippingFeeTest extends TestCase
{
    #[Test]
    public function it_uses_rm12_for_carton_quantity_items(): void
    {
        $controller = new OrderController();
        $items = collect([
            (object) [
                'variant' => (object) [
                    'packing_quantity' => '400 pcs/ctn',
                ],
            ],
        ]);

        $this->assertSame(12.0, $controller->calculateShippingFee($items));
    }

    #[Test]
    public function it_uses_rm5_for_pack_quantity_items(): void
    {
        $controller = new OrderController();
        $items = collect([
            (object) [
                'variant' => (object) [
                    'packing_quantity' => '50 pcs/pkt',
                ],
            ],
        ]);

        $this->assertSame(5.0, $controller->calculateShippingFee($items));
    }
}
