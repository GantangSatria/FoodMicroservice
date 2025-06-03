<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;

class PaymentController extends Controller {
    public function __construct() {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function create(Request $request) {
        $orderId = $request->input('order_id');
        $amount = $request->input('amount');

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount
            ]
        ];

        $snapToken = Snap::getSnapToken($params);
        $redirectUrl = "https://app.midtrans.com/snap/v2/vtweb/{$snapToken}";

        $payment = Payment::create([
            'order_id' => $orderId,
            'amount' => $amount,
            'midtrans_token' => $snapToken,
            'midtrans_redirect_url' => $redirectUrl
        ]);

        return response()->json([
            'payment_id' => $payment->id,
            'snap_token' => $snapToken,
            'redirect_url' => $redirectUrl
        ]);
    }

    public function handleCallback(Request $request) {
        $payload = $request->all();
        $orderId = $payload['order_id'];
        $transactionStatus = $payload['transaction_status'];

        $status = match($transactionStatus) {
            'settlement', 'capture' => 'PAID',
            'expire' => 'EXPIRED',
            'cancel' => 'CANCELLED',
            default => 'PENDING',
        };

        $payment = Payment::where('order_id', $orderId)->first();
        if ($payment) {
            $payment->update(['status' => $status]);
            // Optional: Call Order Service to update status
        }

        return response()->json(['message' => 'Callback handled'], 200);
    }
}
