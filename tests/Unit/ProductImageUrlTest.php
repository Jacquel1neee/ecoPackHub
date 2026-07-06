<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductImageUrlTest extends TestCase
{
    public function test_it_resolves_public_images_paths(): void
    {
        $product = new Product([
            'image' => 'images/HIT_CTR_001.png',
            'image_path' => null,
        ]);

        $this->assertSame('/images/HIT_CTR_001.png', $product->image_url);
    }

    public function test_it_resolves_absolute_public_paths(): void
    {
        $product = new Product([
            'image' => '/images/HIT_CTR_002.png',
            'image_path' => null,
        ]);

        $this->assertSame('/images/HIT_CTR_002.png', $product->image_url);
    }

    public function test_it_resolves_placeholder_urls_to_local_images_when_available(): void
    {
        $product = new Product([
            'code' => 'HIT-CTR-001',
            'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-CTR-001',
            'image_path' => null,
        ]);

        $this->assertSame('/images/HIT_CTR_001.png', $product->image_url);
    }
}
