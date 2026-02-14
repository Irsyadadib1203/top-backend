<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class FFZCallbackController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('FFZ Callback Received', $request->all());

        try {
            // Ambil data dari callback FFZ
            $refId = $request->input('ref_id');
            $status = $request->input('status');
            $message = $request->input('message');

            if (!$refId) {
                return response()->json([
                    'message' => 'Invalid callback data'
                ], 400);
            }

            $transaction = Transaction::where('reference_id', $refId)->first();

            if (!$transaction) {
                return response()->json([
                    'message' => 'Transaction not found'
                ], 404);
            }

            // Mapping status FFZ ke status sistem kamu
            switch (strtolower($status)) {
                case 'success':
                case 'sukses':
                    $transaction->status = 'success';
                    break;

                case 'failed':
                case 'gagal':
                    $transaction->status = 'failed';
                    break;

                case 'pending':
                default:
                    $transaction->status = 'pending';
                    break;
            }

            $transaction->provider_message = $message ?? null;
            $transaction->save();

            return response()->json([
                'message' => 'Callback processed'
            ]);

        } catch (\Exception $e) {
            Log::error('FFZ Callback Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Server error'
            ], 500);
        }
    }
}
