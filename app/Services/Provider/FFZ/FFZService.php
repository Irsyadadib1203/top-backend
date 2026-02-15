<?php

namespace App\Services\Provider\FFZ;

use Illuminate\Support\Facades\Http;
use App\Services\Provider\Contracts\ProviderInterface;
use Illuminate\Support\Facades\Log;

class FFZService implements ProviderInterface
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.ffz.base_url');
        $this->apiKey  = config('services.ffz.api_key');
    }

    public function getCategories(): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->get($this->baseUrl . '/v1/category');

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json();

        return $data['data'] ?? [];
    }


    public function getProducts(string $categoryId = null): array
    {
        Log::info('FFZService getProducts dipanggil', ['category_id' => $categoryId]);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => $this->apiKey,
                ])
                ->get($this->baseUrl . '/v1/products', [
                    'category_id' => $categoryId,
                ]);

            Log::info('Response FFZService', ['status' => $response->status(), 'body' => $response->body()]);

            if (!$response->successful()) {
                throw new \Exception('Gagal ambil produk FFZ: ' . $response->body());
            }

            $data = $response->json();

            if (!is_array($data) || !isset($data['data']) || !is_array($data['data'])) {
                throw new \Exception('Response FFZ tidak valid: ' . json_encode($data));
            }

            $rawProducts = $data['data'];

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

            Log::info('Produk FFZ berhasil diambil', ['count' => count($products)]);

            return $products;


        } catch (\Exception $e) {
            Log::error('Gagal ambil produk FFZ', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'category_id' => $categoryId
            ]);

            return [];

        }
    }
}
