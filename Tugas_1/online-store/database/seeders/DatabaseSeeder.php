<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        //Buat 20 Product Random menggunakan Factory
        $products = Product::factory(20)->create();

        //Buat 10 Order menggunakan Factory
        foreach (range(1, 10) as $index) {
            $product = $products->random();
            $order = Order::create([
                'total_price' => rand(10000, 1000000),
            ]);

            //Buat OrderItem untuk masing-masing order 1~3
            $orderProducts = $products->random(rand(1, 3));
            foreach ($orderProducts as $orderProduct) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $orderProduct->id,
                    'quantity' => rand(1, 3),
                ]);
            }
        }
    }
}
