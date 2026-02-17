<?php

namespace App\Services\Provider\Digiflazz;

use Illuminate\Support\Facades\Http;
use App\Models\Transaction;
use App\Services\Provider\Contracts\TransactionProviderInterface;

class DigiflazzTransactionService implements TransactionProviderInterface
{
    public function createTransaction(Transaction $transaction): array
    {
        $username = config('services.digiflazz.username');
        $key      = config('services.digiflazz.key');

        $refId = $transaction->ref_id;
        $signature = md5($username . $key . $refId);

        $nominal = $transaction->nominal;

        if (!$nominal || !$nominal->provider_product_code) {
            throw new \Exception('Nominal tidak valid');
        }

        $response = Http::timeout(20)
            ->retry(3, 1000)
            ->post(config('services.digiflazz.base_url') . '/v1/transaction', [
                'username' => $username,
                'sign' => $signature,
                'buyer_sku_code' => $nominal->provider_product_code,
                'customer_no' => $transaction->customer_id,
                'ref_id' => $refId,
            ]);

        if (!$response->successful()) {
            return [
                'success' => false,
                'message' => $response->body()
            ];
        }

       $result = $response->json();

        if (($result['data']['status'] ?? '') !== 'Sukses') {
            return [
                'success' => false,
                'message' => $result['data']['message'] ?? 'Transaksi gagal'
            ];
        }

        return [
            'success' => true,
            'data' => $result['data']
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
