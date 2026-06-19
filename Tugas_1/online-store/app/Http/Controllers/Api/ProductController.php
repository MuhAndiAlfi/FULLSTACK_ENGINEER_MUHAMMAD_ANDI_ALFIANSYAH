<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product as ProductModel;

class ProductController extends Controller
{
    //End Point List Product
    public function index()
    {
        //Memanggil Method Product yang ada di Model
        $products = ProductModel::products();

        if ($products->count() > 0) {

            //Response Data dari model
            return response()->json([
                "status" => "success",
                "message" => "Product list",
                "data" => $products
            ], 200);
        }

        return response()->json([
            "status" => "error",
            "message" => "Product not found",
            "data" => null
        ], 404);
    }

    //End Point Find Product
    public function show($id)
    {
        $product = ProductModel::showProduct($id);

        if (!$product) {
            return response()->json([
                "status" => "error",
                "message" => "Product not found",
                "data" => null
            ], 404);
        }

        return response()->json([
            "status" => "success",
            "message" => "Product detail",
            "data" => $product
        ], 200);
    }
}
