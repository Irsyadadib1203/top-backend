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
        $newStatus = $this->mapProviderStatus($result['provider_status'] ?? 'PENDING');
        
        $transaction->update([
            'previous_status' => $transaction->status,
            'status' => $newStatus,
            'status_version' => $transaction->status_version + 1,
            'provider_order_id' => $result['provider_ref'] ?? null,
            'provider_status' => strtolower($result['provider_status'] ?? 'pending'),
            'provider_message' => $result['message'] ?? null,
            'provider_callback_data' => $result['raw'] ?? null,
            'completed_at' => in_array($newStatus, ['success','failed']) ? now() : null,
        ]);
    }


    public function mapProviderStatus(string $providerStatus): string
    {
        return match (strtoupper($providerStatus)) {
            'SUCCESS', 'SUKSES', 'PAID' => 'success',
            'PENDING' => 'processing',
            'PARTIAL_SUCCESS' => 'partial',
            'FAILED', 'GAGAL', 'REFUNDED' => 'failed',
            default => 'processing',
        };
    }

    public function checkStatus(Transaction $transaction): array
    {
        $providerCode = $transaction->nominal->provider->code;

        $provider = $this->providerManager->transactionDriver($providerCode);

        return $provider->checkStatus($transaction);
    }


}
