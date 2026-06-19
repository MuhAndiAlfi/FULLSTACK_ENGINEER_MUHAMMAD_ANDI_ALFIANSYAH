<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
    ];

    public static function createOrderItem(int $orderId, int $productId, int $quantity)
    {
        return self::create([
            "order_id" => $orderId,
            "product_id" => $productId,
            "quantity" => $quantity,
        ]);
    }
}
