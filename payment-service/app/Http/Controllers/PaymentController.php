<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

// $client = new Client();

class PaymentController extends Controller
{

    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function create(Request $request)
    {
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

    public function handleCallback(Request $request)
    {
        $payload = $request->all();
        $orderId = $payload['order_id'];
        $transactionStatus = $payload['transaction_status'];

        $status = match ($transactionStatus) {
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

    // public function handleWebhook(Request $request)
    // {
    //     $payload = $request->all();
    //     Log::info('Midtrans Webhook Payload:', $payload);

    //     // Ambil config server key dari .env
    //     $serverKey = env('MIDTRANS_SERVER_KEY');

    //     // Ambil signature_key yang dikirim Midtrans
    //     $receivedSignature = $payload['signature_key'] ?? '';

    //     // Buat signature dari payload (signature_key harus sesuai dokumentasi Midtrans)
    //     $orderId = $payload['order_id'] ?? '';
    //     $statusCode = $payload['status_code'] ?? '';
    //     $grossAmount = $payload['gross_amount'] ?? '';
    //     $transactionStatus = $payload['transaction_status'] ?? '';

    //     $inputString = $orderId . $statusCode . $grossAmount . $serverKey;
    //     $computedSignature = hash('sha512', $inputString);

    //     if ($receivedSignature !== $computedSignature) {
    //         Log::warning('Midtrans webhook signature mismatch!');
    //         return response()->json(['message' => 'Invalid signature'], 403);
    //     }

    //     // Proses update status payment berdasarkan transaction_status
    //     $payment = Payment::where('order_id', $orderId)->first();

    //     if (!$payment) {
    //         return response()->json(['message' => 'Payment not found'], 404);
    //     }

    //     // Update status sesuai transaction_status Midtrans
    //     $payment->status = $transactionStatus; // contoh: 'settlement', 'pending', 'deny', dsb
    //     $payment->save();

    //     if ($transactionStatus === 'settlement') {
    //         $payment->status = 'paid';
    //         $payment->save();

    //         // Call Order Service
    //         $client = new Client();
    //         try {
    //             $client->patch("http://localhost:8001/orders/{$orderId}/payment-status", [
    //                 'json' => ['payment_status' => 'paid']
    //             ]);
    //         } catch (\Exception $e) {
    //             Log::error("Order Service update failed: " . $e->getMessage());
    //         }
    //     }


    //     return response()->json(['message' => 'Webhook processed']);
    // }

        public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook Payload:', $payload);

        $serverKey = env('MIDTRANS_SERVER_KEY');
        $receivedSignature = $payload['signature_key'] ?? '';
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';

        // Validate signature
        $inputString = $orderId . $statusCode . $grossAmount . $serverKey;
        $computedSignature = hash('sha512', $inputString);

        if ($receivedSignature !== $computedSignature) {
            Log::warning('Midtrans webhook signature mismatch!');
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Find payment
        $payment = Payment::where('order_id', $orderId)->first();
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // Determine internal status
        $internalStatus = match($transactionStatus) {
            'settlement', 'capture' => 'paid',
            'cancel' => 'cancelled',
            'expire' => 'expired',
            default => 'pending',
        };

        $payment->status = $internalStatus;
        $payment->save();

        // Notify Order Service if successful
        if ($internalStatus === 'paid') {
            $client = new Client();
            $orderServiceUrl = env('ORDER_SERVICE_URL');

            try {
                $response = $client->patch("{$orderServiceUrl}/orders/{$orderId}/payment-status", [
                    'json' => ['payment_status' => 'paid'],
                    'timeout' => 5, // Optional: timeout in seconds
                ]);
                Log::info("Order Service updated for order {$orderId}");
            } catch (\Exception $e) {
                Log::error("Order Service update failed: " . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Webhook processed']);
    }

}
