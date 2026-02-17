<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;

class RetryPendingTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $transactions = Transaction::query()
            ->where('status', 'processing')
            ->whereColumn('retry_count', '<', 'max_retries')
            ->where(function ($q) {
                $q->whereNull('last_retry_at')
                ->orWhere('last_retry_at', '<', now()->subMinutes(3));
            })
            ->limit(50)
            ->get();

        foreach ($transactions as $trx) {

            try {

                /** @var \App\Models\Transaction $transaction */
                $transaction = Transaction::find($trx->id);

                if (!$transaction) {
                    continue;
                }

                // increment retry safely
                $transaction->increment('retry_count');
                $transaction->update([
                    'last_retry_at' => now()
                ]);

                if (in_array($transaction->status, ['success', 'failed'])) {
                    continue;
                }

                $transactionService = app(TransactionService::class);
                $result = $transactionService->checkStatus($transaction);

                if (!$result['success']) {
                    continue;
                }

                $newStatus = $transactionService->mapProviderStatus(
                    $result['provider_status']
                );

                if ($newStatus !== $transaction->status) {

                    $transaction->update([
                        'previous_status' => $transaction->status,
                        'status' => $newStatus,
                        'provider_status' => $result['provider_status'],
                        'provider_message' => $result['message'] ?? null,
                        'provider_callback_data' => $result['raw'] ?? null,
                        'status_version' => $transaction->status_version + 1,
                        'completed_at' => $transaction->completed_at
                            ?? (in_array($newStatus, ['success','failed']) ? now() : null),
                    ]);
                }

            } catch (\Throwable $e) {
                Log::error("Retry failed", [
                    'invoice' => $trx->invoice_number ?? null,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
