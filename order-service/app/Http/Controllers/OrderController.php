<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;


class OrderController extends Controller
{
    public function index()
    {
        return response()->json(Order::with('items')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_uuid' => 'required|uuid',
            'restaurant_id' => 'required|integer',
            'total_amount' => 'required|numeric',
            'delivery_address' => 'required|array',
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|integer',
            'items.*.menu_item_snapshot' => 'required|array',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.total_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        $order = Order::create([
            'user_uuid' => $data['user_uuid'],
            'restaurant_id' => $data['restaurant_id'],
            'total_amount' => $data['total_amount'],
            'delivery_address' => $data['delivery_address'],
        ]);

        foreach ($data['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item_id'],
                'menu_item_snapshot' => $item['menu_item_snapshot'],
                'quantity' => $item['quantity'],
                'total_price' => $item['total_price']
            ]);
        }

        $client = new Client();
        try {
            $response = $client->post('http://localhost:8000/payments', [
                'json' => [
                    'order_id' => $order->uuid,
                    'amount' => $order->total_amount,
                ]
            ]);

            $paymentResponse = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            \Log::error('Payment service call failed: ' . $e->getMessage());
        }

        return response()->json($order->load('items'), 201);
    }

    public function show($uuid)
    {
        $order = Order::with('items')->where('uuid', $uuid)->firstOrFail();
        return response()->json($order);
    }

    public function updatePaymentStatus(Request $request, $uuid)
    {
        $this->validate($request, [
            'payment_status' => 'required|in:pending,paid,failed'
        ]);

        $order = Order::where('uuid', $uuid)->firstOrFail();
        $order->payment_status = $request->input('payment_status');
        $order->save();

        return response()->json(['message' => 'Payment status updated']);
    }

}
