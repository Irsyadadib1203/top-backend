<?php

namespace App\Services\Provider;

use App\Services\Provider\Contracts\ProviderInterface;
use App\Services\Provider\Digiflazz\DigiflazzTransactionService;
use App\Services\Provider\FFZ\FFZTransactionService;
use App\Services\Provider\Contracts\TransactionProviderInterface;
use App\Services\Provider\Digiflazz\DigiflazzService;
use App\Services\Provider\FFZ\FFZService;
use InvalidArgumentException;

class ProviderManager
{
    public function driver(string $providerCode): ProviderInterface
    {
        return match (strtoupper($providerCode)) {
            'FFZ' => app(FFZService::class),
            'DIGIFLAZZ' => app(DigiflazzService::class),
            default => throw new InvalidArgumentException("Provider tidak ditemukan"),
        };
    }
    public function transactionDriver(string $providerCode): TransactionProviderInterface
    {
        return match (strtoupper(trim($providerCode))) {
            'DIGIFLAZZ' => app(DigiflazzTransactionService::class),
            'FFZ' => app(FFZTransactionService::class),
            default => throw new InvalidArgumentException("Provider tidak ditemukan"),
        };
    }
}
