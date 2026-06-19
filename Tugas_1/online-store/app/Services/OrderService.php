<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    /**
     * Store a new order and decrement product stock.
     *
     * @param array $data
     * @return Order
     * @throws Exception
     */
    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            // pakai lockforupdate untuk membatasi akses concurrent saat product dibeli
            $product = Product::lockForUpdate()->find($data['product_id']);


            if (!$product) {
                throw new Exception("Product not found", 404);
            }

            if ($product->stock < $data['quantity']) {
                throw new Exception("Stock not enough", 400);
            }

            // kurangi jumlah stok kalau ketemu
            $product->decrement('stock', $data['quantity']);

            // Hitung total harga
            $totalPrice = $product->price * $data['quantity'];

            // kirim ke order untuk disimpan
            $order = Order::createOrder($totalPrice);

            // kirim ke order item untuk disimpan
            $orderItem = OrderItem::createOrderItem($order->id, $product->id, $data['quantity']);

            // Format data order
            $response = [
                "id" => $order->id,
                "total_price" => $order->total_price,
                "order_item" => $orderItem,
                "created_at" => $order->created_at->format("Y-m-d H:i:s"),
                "updated_at" => $order->updated_at->format("Y-m-d H:i:s"),
            ];

            return $response;
        });
    }

}
