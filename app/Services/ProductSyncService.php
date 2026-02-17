<?php

namespace App\Services;

use App\Models\Nominal;
use App\Models\Game;
use App\Models\Provider;
use App\Models\GameProvider;
use App\Services\Provider\ProviderManager;

class ProductSyncService
{
    protected ProviderManager $providerManager;

    public function __construct(ProviderManager $providerManager)
    {
        $this->providerManager = $providerManager;
    }

    public function sync(Provider $provider, string $gameId, ?string $categoryId = null): int
    {
        $driver = $this->providerManager->driver($provider->code);

        $gameProvider = GameProvider::where('game_id', $gameId)
            ->where('provider_code', $provider->code)
            ->firstOrFail();

        $products = $provider->getProducts($gameProvider->provider_category_id);
        
        if (empty($products)) {
            throw new \Exception("Produk kosong dari provider");
        }


        $game = Game::findOrFail($gameId);

        $savedCount = 0;

        foreach ($products as $product) {
            if (!$product['is_active']) {
                continue;
            }
            Nominal::updateOrCreate(
                [
                    'provider_id' => $provider->id,
                    'provider_product_code' => $product['code']
                    ],
                [
                    'game_id' => $game->id,
                    'name' => $product['name'],
                    'base_price' => $product['price'],
                    'selling_price' => $product['price'] * 1.10,
                    'margin_percent' => 10,
                    'is_active' => true,
                ]
            );

            $savedCount++;
        }

        return $savedCount; // ✅ WAJIB ADA
    }
}
