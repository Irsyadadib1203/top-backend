<?php

namespace App\Services\Provider\Contracts;

interface ProviderInterface
{
    public function getProducts(?string $categoryId = null): array;
}
