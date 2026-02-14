<?php

namespace App\Services;

use App\Models\Transaction;
use App\Services\Provider\ProviderManager;

class TransactionService
{
    protected ProviderManager $providerManager;

    public function __construct(ProviderManager $providerManager)
    {
        $this->providerManager = $providerManager;
    }

    public function process(Transaction $transaction)
    {
        $providerCode = $transaction->nominal->provider->code;

        $provider = $this->providerManager->transactionDriver($providerCode);

        $result = $provider->createTransaction($transaction);

        if (!$result['success']) {
            $transaction->update([
                'provider_status' => 'failed',
                'provider_message' => $result['message'] ?? null,
                'provider_callback_data' => $result['raw'] ?? null,
                'status' => 'failed',
            ]);

            return;
        }

        $transaction->update([
            'provider_order_id' => $result['provider_ref'] ?? null,
            'provider_status' => 'success',
            'provider_message' => $result['message'] ?? null,
            'provider_callback_data' => $result['raw'] ?? null,
            'status' => 'success',
        ]);
    }


    private function mapStatus(string $providerStatus): string
    {
        return match (strtoupper($providerStatus)) {
            'SUCCESS', 'PAID' => 'success',
            'PENDING' => 'processing',
            'PARTIAL_SUCCESS' => 'partial',
            'FAILED', 'REFUNDED' => 'failed',
            default => 'processing',
        };
    }

}
