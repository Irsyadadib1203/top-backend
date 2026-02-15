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
        $signature = md5($this->username . $this->apiKey . "pricelist");

        try {
            $response = Http::timeout(30)
                ->post($this->baseUrl . '/v1/price-list', [
                    'username'  => $this->username,
                    'sign' => $signature,
                ]);

            if (!$response->successful()) {
                throw new \Exception('Gagal ambil produk Digiflazz: ' . $response->body());
            }

            $data = $response->json();

            // ✅ Pastikan response valid dan data ada
            if (!is_array($data) || !isset($data['data']) || !is_array($data['data'])) {
                throw new \Exception('Response Digiflazz tidak valid: ' . json_encode($data));
            }

            $rawProducts = $data['data'];

             if ($categoryId) {
                $rawProducts = collect($rawProducts)
                    ->filter(fn($item) => $item['category'] === $categoryId)
                    ->values()
                    ->toArray();
            }

            // 🔥 Normalisasi format seperti command lama
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

            return $products;

        } catch (\Exception $e) {
            // ❌ Log error untuk debugging
            \Log::error('Gagal ambil produk Digiflazz', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            return []; // ✅ Kembalikan array kosong jika gagal, jangan lempar exception
        }
    }

}
