<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;

class RetryPendingTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Ambil transaksi yang masih processing > 5 menit
        $timeout = now()->subMinutes(5);
        $transactions = Transaction::where('status', 'processing')
            ->where('updated_at', '<', $timeout)
            ->get();
        
/** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            try {
                $username = config('services.digiflazz.username');
                $key = config('services.digiflazz.key');
                $refId = $transaction->invoice_number;
                $signature = md5($username . $key . $refId);

                // Panggil API Digiflazz untuk cek status transaksi
                $response = Http::timeout(10)->post('https://api.digiflazz.com/v1/transaction-status', [
                    'username' => $username,
                    'signature' => $signature,
                    'ref_id' => $refId,
                ]);

                $data = $response->json();

                if (isset($data['data']['status'])) {
                    $status = strtolower($data['data']['status']);
                    $transaction->update([
                        'status' => $status === 'success' ? 'success' : 'failed',
                        'provider_status' => $status,
                        'provider_message' => $data['data']['message'] ?? null,
                        'provider_callback_data' => $data,
                    ]);

                    Log::info("RetryPendingTransactions: updated {$transaction->invoice_number} to {$status}");
                } else {
                    Log::warning("RetryPendingTransactions: status unknown for {$transaction->invoice_number}", $data);
                }
            } catch (\Exception $e) {
                Log::error("RetryPendingTransactions failed for {$transaction->invoice_number}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
