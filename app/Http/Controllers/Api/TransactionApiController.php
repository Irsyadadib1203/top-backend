<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Jobs\ProcessTransaction;
use App\Models\Nominal;

class TransactionApiController extends Controller
{
    public function store(Request $request)
    {
        \DB::beginTransaction();
        try {
           
            \Log::info('Transaction create request', $request->all());

            $request->validate([
                'invoice_number' => 'required|string|unique:transactions,invoice_number',
                'customer_id' => 'required|string',
                'customer_phone' => 'nullable|string',
                'game_id' => 'required|exists:games,id',  // Pastikan game ada
                'nominal_id' => 'required|exists:nominals,id',  // Pastikan nominal ada
                'base_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'admin_fee' => 'nullable|numeric|min:0',
                'status' => 'nullable|string|in:pending,processing,success,failed',
            ]);

            $nominal = Nominal::findOrFail($request->nominal_id);
            $totalAmount = $nominal->selling_price + ($request->admin_fee ?? 0);
            $transaction = Transaction::create([
                'invoice_number' => $request->invoice_number,
                'customer_id' => $request->customer_id,
                'customer_phone' => $request->customer_phone,
                'game_id' => $request->game_id,
                'nominal_id' => $request->nominal_id,
                'provider_id' => $nominal->provider_id,
                'base_price' => $request->base_price,
                'selling_price' => $request->selling_price,
                'admin_fee' => $request->admin_fee ?? 0,
                'total_amount' => $totalAmount,
                'status' => $request->status ?? 'pending',
                'ref_id' => Str::random(10),
            ]);

            $transaction->load('game', 'nominal');

            \Log::info('Transaction created successfully', ['id' => $transaction->id]);
           
            ProcessTransaction::dispatch($transaction);
            \DB::commit();

            \Log::info('ProcessTransaction job dispatched', ['transaction_id' => $transaction->id]);
               
                return response()->json([
                'id' => $transaction->id,
                'invoice_number' => $transaction->invoice_number,
                'customer_id' => $transaction->customer_id,
                'customer_phone' => $transaction->customer_phone,
                'game_id' => $transaction->game_id,
                'nominal_id' => $transaction->nominal_id,
                'base_price' => $transaction->base_price,
                'selling_price' => $transaction->selling_price,
                'admin_fee' => $transaction->admin_fee,
                'total_amount' => $transaction->total_amount,
                'status' => $transaction->status,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
                'game_name' => $transaction->game?->name ?? '-',
                'nominal_name' => $transaction->nominal?->name ?? '-',
                'ref_id' => $transaction->ref_id,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Transaction create error: ' . $e->getMessage() . ' | Line: ' . $e->getLine() . ' | File: ' . $e->getFile());
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($invoiceId)
    {
        try {
            $transaction = Transaction::with('game', 'nominal')
                ->where('invoice_number', $invoiceId)
                ->first();

            if (!$transaction) {
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            return response()->json([
                'id' => $transaction->id,
                'invoice_number' => $transaction->invoice_number,
                'customer_id' => $transaction->customer_id,
                'server_id' => $transaction->server_id ?? null,
                'total_amount' => $transaction->total_amount,
                'status' => $transaction->status,
                'payment_method' => $transaction->payment_method,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
                'game_name' => $transaction->game?->name ?? '-',
                'nominal_name' => $transaction->nominal?->name ?? '-',
            ]);
        } catch (\Exception $e) {
            \Log::error('Transaction show error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function recent(Request $request)
    {
        try {
            $limit = $request->query('limit', 15);

            $transactions = Transaction::with('game', 'nominal')
                ->where('status', 'success')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            $data = $transactions->map(function ($tx) {
                return [
                    'id' => $tx->id,
                    'invoice_number' => $tx->invoice_number,
                    'customer_id' => $tx->customer_id,
                    'server_id' => $tx->server_id ?? null,
                    'total_amount' => $tx->total_amount,
                    'status' => $tx->status,
                    'payment_method' => $tx->payment_method,
                    'created_at' => $tx->created_at,
                    'updated_at' => $tx->updated_at,
                    'game_name' => $tx->game?->name ?? '-',
                    'nominal_name' => $tx->nominal?->name ?? '-',
                ];
            });

            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Transaction recent error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    public function process(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        // Validasi: hanya process jika status pending
        if ($transaction->status !== 'pending') {
            return response()->json(['message' => 'Transaction sudah diproses'], 400);
        }

        // Dispatch job untuk process async
        ProcessTransaction::dispatch($transaction);

        return response()->json(['message' => 'Transaction sedang diproses']);
    }
}