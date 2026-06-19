<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_price',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, "order_id", "id");
    }

    public static function orders()
    {
        $data = self::orderBy("id", "desc")->get();
        $response = [];

        foreach ($data as $item) {
            $response[] = [
                "id" => $item->id,
                "total_price" => $item->total_price,
                "order_items" => $item->orderItems,
                "created_at" => $item->created_at->format("Y-m-d H:i:s"),
                "updated_at" => $item->updated_at->format("Y-m-d H:i:s"),
            ];
        }
        return $response;
    }

    public static function showOrder($id)
    {
        $data = self::find($id);

        if (!$data) {
            return null;
        }

        return $data;
    }

    public static function createOrder(int $totalPrice)
    {
        return self::create([
            "total_price" => $totalPrice
        ]);
    }
}
