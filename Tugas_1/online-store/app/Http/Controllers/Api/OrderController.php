<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Models\Order as OrderModel;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {

        $orders = OrderModel::orders();

        return response()->json([
            "status" => "success",
            "message" => "Order list",
            "data" => $orders
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $order = $this->orderService->createOrder($validated);

            return response()->json([
                "status" => "success",
                "message" => "Order created successfully",
                "data" => $order
            ], 201);
        } catch (\Exception $e) {
            $statusCode = $e->getCode();
            if (!is_int($statusCode) || $statusCode < 400 || $statusCode > 599) {
                $statusCode = 500;
            }

            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => null
            ], $statusCode);
        }
    }
}
