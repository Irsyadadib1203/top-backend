<?php

namespace App\Services\Provider\Digiflazz;

use Illuminate\Support\Facades\Http;
use App\Services\Provider\Contracts\ProviderInterface;

class DigiflazzService implements ProviderInterface
{
    protected string $baseUrl;
    protected string $username;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl  = config('services.digiflazz.base_url');
        $this->username = config('services.digiflazz.username');
        $this->apiKey   = config('services.digiflazz.key');
    }

    public function getProducts(?string $categoryId = null): array
    {
        $signature = md5($this->username . $this->apiKey);

        $response = Http::timeout(30)
            ->post($this->baseUrl . '/v1/price-list', [
                'username'  => $this->username,
                'signature' => $signature,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Gagal ambil produk Digiflazz: ' . $response->body());
        }

        $rawProducts = $response->json()['data'] ?? [];

        // 🔥 Normalisasi format
        $products = collect($rawProducts)->map(function ($item) {
            return [
                'code'      => $item['buyer_sku_code'] ?? null,
                'name'      => $item['product_name'] ?? null,
                'price'     => $item['price'] ?? 0,
                'is_active' => ($item['buyer_product_status'] ?? false)
                                && ($item['seller_product_status'] ?? false),
                'raw'       => $item
            ];
        })->toArray();

        return [
            'status' => true,
            'data'   => $products
        ];
    }
}
