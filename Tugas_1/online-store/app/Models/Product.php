<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'stock',
    ];

    public static function products()
    {
        $product = Product::orderBy('id', 'desc')->paginate(10);
        return $product;
    }

    public static function showProduct($id)
    {
        $product = Product::where('id', $id)->first();
        return $product;
    }
}
