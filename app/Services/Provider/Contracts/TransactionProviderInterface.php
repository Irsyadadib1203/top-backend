<?php

namespace App\Services\Provider\Contracts;

use App\Models\Transaction;

interface TransactionProviderInterface
{
    public function createTransaction(Transaction $transaction): array;
    public function checkStatus(Transaction $transaction): array;

}
