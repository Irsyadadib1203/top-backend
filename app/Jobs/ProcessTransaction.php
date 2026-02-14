<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessTransaction implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function handle(TransactionService $transactionService)
    {
        $transaction = $this->transaction->fresh();

        if (!$transaction || $transaction->status !== 'pending') {
            return;
        }

        $transactionService->process($transaction);
    }
}
