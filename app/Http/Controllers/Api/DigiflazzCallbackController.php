<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class DigiflazzCallbackController extends Controller
{
    public function handle(Request $request)
    {
        // Log raw callback (penting untuk debug)
        Log::info('Digiflazz Callback', $request->all());

        $username = config('services.digiflazz.username');
        $apiKey   = config('services.digiflazz.key');

        /**
         * Payload Digiflazz umumnya:
         * {
         *   ref_id,
         *   status,
         *   message,
         *   buyer_sku_code,
         *   customer_no,
         *   price,
         *   signature
         * }
         */

        $refId = $request->ref_id;
        $status = strtolower($request->status);
        $signature = $request->signature;

        if (!$refId || !$signature) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // Validasi signature callback
        $expectedSignature = md5($username . $apiKey . $refId);

        if ($signature !== $expectedSignature) {
            Log::warning('Digiflazz Callback Invalid Signature', [
                'ref_id' => $refId,
            ]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Cari transaksi
        $transaction = Transaction::where('invoice_number', $refId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Idempotent: kalau sudah final, jangan diproses lagi
        if (in_array($transaction->status, ['success', 'failed'])) {
            return response()->json(['message' => 'Already processed']);
        }

        // Mapping status Digiflazz → sistem kamu
        if ($status === 'success') {
            $transaction->update([
                'status' => 'success',
                'provider_status' => 'success',
                'provider_message' => $request->message,
                'provider_callback_data' => $request->all(),
                'completed_at' => now(),
            ]);

            /**
             * TODO PENTING:
             * - kirim saldo
             * - grant item
             * - notify user
             */

        } elseif ($status === 'failed') {
            $transaction->update([
                'status' => 'failed',
                'provider_status' => 'failed',
                'provider_message' => $request->message,
                'provider_callback_data' => $request->all(),
            ]);
        } else {
            // pending / processing
            $transaction->update([
                'status' => 'processing',
                'provider_status' => $status,
                'provider_callback_data' => $request->all(),
            ]);
        }

        return response()->json(['message' => 'Callback processed']);
    }
}
