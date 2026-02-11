<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Nominal;

class UpdatePricesFromDigiflazz extends Command
{
    protected $signature = 'digiflazz:update-prices';
    protected $description = 'Update harga produk dari DigiFlazz setiap 5 menit';

    public function handle()
    {
        $username = config('services.digiflazz.username');
        $key = config('services.digiflazz.key');
        $signature = md5($username . $key );

        try {
            $response = Http::post('https://api.digiflazz.com/v1/price-list', [
                'username' => $username,
                'signature' => $signature,
            ]);

            if (!$response->successful()) {
                $this->error('Gagal fetch dari DigiFlazz: ' . $response->body());
                return;
            }

            $data = $response->json();
            $products = $data['data'] ?? [];
            $updatedCount = 0;
            $nominals = Nominal::all()->keyBy('provider_product_code');

            foreach ($products as $product) {
                if (
                    isset($nominals[$product['buyer_sku_code']]) &&
                    $product['buyer_product_status'] &&
                    $product['seller_product_status']
                ) {
                    $nominal = $nominals[$product['buyer_sku_code']];
                    $marginPercent = $nominal->margin_percent ?? 10;
                    $newSellingPrice = $product['price'] * (1 + $marginPercent / 100);

                    if (
                        $nominal->base_price != $product['price'] ||
                        $nominal->selling_price != $newSellingPrice
                    ) {
                        $nominal->update([
                            'base_price' => $product['price'],
                            'selling_price' => $newSellingPrice,
                            'is_active' => true,
                        ]);

                        $updatedCount++;
                    }
                }
            }

            $this->info("Berhasil update {$updatedCount} harga produk.");
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}