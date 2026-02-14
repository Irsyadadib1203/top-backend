<?php

namespace App\Services\Provider\FFZ;

use Illuminate\Support\Facades\Http;
use App\Services\Provider\Contracts\ProviderInterface;

class FFZService implements ProviderInterface
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.ffz.base_url');
        $this->apiKey  = config('services.ffz.api_key');
    }

    public function getProducts(string $categoryId = null): array
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => $this->apiKey,
            ])
            ->get($this->baseUrl . '/v1/products', [
                'category_id' => $categoryId,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Gagal ambil produk FFZ: ' . $response->body());
        }

        $rawProducts = $response->json()['data'] ?? [];

        // 🔥 Normalisasi format
        $products = collect($rawProducts)->map(function ($item) {
           return [
                'code'      => $item['product_code'] ?? null,
                'name'      => $item['product_name'] ?? null,
                'price'     => $item['product_price'] ?? 0,
                'is_active' => $item['is_active'] ?? false,
                'raw'       => $item
            ];

        })->toArray();

        return [
            'status' => true,
            'data'   => $products
        ];
    }
}
