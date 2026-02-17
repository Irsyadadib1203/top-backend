<?php

namespace App\Services\Provider\FFZ;

use Illuminate\Support\Facades\Http;
use App\Models\Transaction;
use App\Services\Provider\Contracts\TransactionProviderInterface;

class FFZTransactionService implements TransactionProviderInterface
{
    public function createTransaction(Transaction $transaction): array
{
    $apiKey  = config('services.ffz.api_key');
    $baseUrl = config('services.ffz.base_url');

    $nominal = $transaction->nominal;

    if (!$nominal || !$nominal->provider_product_code) {
        return [
            'success' => false,
            'message' => 'Nominal tidak valid'
        ];
    }

    $payload = [
        'product_code' => $nominal->provider_product_code,
        'user_id'      => $transaction->customer_id,
        'server_id'    => $transaction->server_id ?? null,
        'trx_id'       => $transaction->ref_id,
        'callback_url' => route('callback.ffz'),
    ];

    $response = Http::timeout(20)
        ->retry(3, 1000)
        ->withHeaders([
            'Authorization' => $apiKey,
            'Content-Type'  => 'application/json',
        ])
        ->post($baseUrl . '/v1/order', $payload);

    if (!$response->successful()) {
        return [
            'success' => false,
            'message' => $response->body()
        ];
    }

    $data = $response->json();

    if (($data['statusCode'] ?? 500) !== 200) {
        return [
            'success' => false,
            'message' => $data['message'] ?? 'FFZ order gagal',
            'raw' => $data
        ];
    }

    return [
        'success' => true,
        'provider_ref' => $data['data']['trxId'] ?? null,
        'raw' => $data
    ];
}
public function checkStatus(Transaction $transaction): array
{
    return [
        'success' => false,
        'message' => 'Provider does not support status check'
    ];
}


}
