<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Transaction;

class ProcessDigiflazzTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function handle()
    {
        $username = config('services.digiflazz.username');
        $key = config('services.digiflazz.key');

        $refId = $this->transaction->invoice_number;
        $signature = md5($username . $key . $refId);

        $nominal = $this->transaction->nominal;

        if (!$nominal || !$nominal->provider_product_code) {
            $this->fail('Nominal tidak valid');
            return;
        }

        try {
                    \Log::info('Digiflazz Request', [
                'username' => $username,
                'signature' => $signature,
                'buyer_sku_code' => $nominal->provider_product_code,
                'customer_no' => $this->transaction->customer_id,
                'ref_id' => $refId,
                'testing' => config('services.digiflazz.testing', false),
            ]);
            $response = Http::timeout(20)
                ->retry(3, 1000)
                ->post('https://api.digiflazz.com/v1/transaction', [
                    'username' => $username,
                    'signature' => $signature,
                    'buyer_sku_code' => $nominal->provider_product_code,
                    'customer_no' => $this->transaction->customer_id,
                    'ref_id' => $refId,
                    'testing' => config('services.digiflazz.testing', false),
                ]);
                        \Log::info('Digiflazz Response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            $data = $response->json();
            

            if (!$response->successful()) {
                throw new \Exception($data['message'] ?? 'Digiflazz error');
                
            }

            $providerStatus = strtolower($data['data']['status'] ?? 'pending');

            $this->transaction->update([
                'status' => 'processing',
                'provider_status' => $providerStatus,
                'provider_message' => $data['data']['message'] ?? null,
                'provider_callback_data' => $data,
            ]);

        } catch (\Throwable $e) {
            $this->transaction->update([
                'status' => 'failed',
                'provider_status' => 'error',
                'provider_message' => $e->getMessage(),
            ]);

            $this->fail($e->getMessage());
        }
    }

}