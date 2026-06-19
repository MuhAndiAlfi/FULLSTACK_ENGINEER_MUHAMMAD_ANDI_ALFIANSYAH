<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\Order;
use Tests\TestCase;

class OrderRaceConditionTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test order kalau stock tidak cukup
     */
    public function test_OrderStockNotEnough(): void
    {
        $product = Product::factory()->create([
            'price' => 20000,
            'stock' => 2,
        ]);

        $response = $this->postJson('/api/orders', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response->assertStatus(400)->assertJson([
            'status' => 'error',
            'message' => 'Stock not enough'
        ]);

        $product->refresh();
        $this->assertEquals(2, $product->stock);
        $this->assertEquals(0, Order::count());
    }
}
