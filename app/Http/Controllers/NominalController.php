<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Nominal;
use App\Models\Game;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\ProductSyncService;
use Illuminate\Support\Facades\Log;

class NominalController extends Controller
{
    public function index(Request $request)
    {
         $query = Nominal::with('game');

        // Search nama nominal
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter game
        if ($request->game) {
            $query->where('game_id', $request->game);
        }
        
         if ($request->provider) {
            $query->whereHas('game', function ($q) use ($request) {
                $q->where('provider_id', $request->provider);
            });
        }
        
        // Filter status
        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status);
        }
        $nominals = $query->orderBy('created_at', 'desc')->paginate(10);

        $games = Game::where('is_active', 1)->get();

        $provider=Provider::get();

        return view('admin.produk.nominal', compact('nominals', 'games', 'provider'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'game_id'       => 'required|exists:games,id',
            'name'          => 'required|string|max:100',
            'base_price'    => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
        ]);

        Nominal::create([
            'game_id'       => $request->game_id,
            'provider_id'   => $request->provider_id ?? null,
            'name'          => $request->name,
            'base_price'    => $request->base_price,
            'selling_price' => $request->selling_price,
            'is_active'     => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Nominal berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $nominal = Nominal::findOrFail($id);

        $request->validate([
            'game_id'       => 'required|exists:games,id',
            'name'          => 'required|string|max:100',
            'base_price'    => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
        ]);

        $nominal->update([
            'game_id'       => $request->game_id,
            'name'          => $request->name,
            'base_price'    => $request->base_price,
            'selling_price' => $request->selling_price,
            'is_active'     => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Nominal berhasil diperbarui');
    }

    public function destroy($id)
    {
        Nominal::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Nominal berhasil dihapus');
    }
    public function fetchFromDigiflazz(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
        ]);

        $username = config('services.digiflazz.username');
        $key = config('services.digiflazz.key');
        $signature = md5($username . $key );

        try {
            $response = Http::post('https://api.digiflazz.com/v1/price-list', [
                'username' => $username,
                'signature' => $signature
            ]);

            if (!$response->successful()) {
                return redirect()->back()->withErrors('Gagal mengambil data dari DigiFlazz: ' . $response->body());
            }

            $data = $response->json();
            $products = $data['data'] ?? [];

            $savedCount = 0;
            $game = Game::find($request->game_id);

            foreach ($products as $product) {
                if (
                    stripos($product['product_name'], $game->name) !== false &&
                    $product['buyer_product_status'] &&
                    $product['seller_product_status']
                ) {
                    Nominal::firstOrCreate(
                        ['provider_product_code' => $product['buyer_sku_code']],
                        [
                            'game_id' => $game->id,
                            'name' => $product['product_name'],
                            'base_price' => $product['price'],
                            'selling_price' => $product['price'] * 1.10,
                            'margin_percent' => 10,
                            'is_active' => true,
                        ]
                    );

                    $savedCount++;
                }
            }


            return redirect()->back()->with('success', "Berhasil menyimpan {$savedCount} produk dari DigiFlazz.");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }
    public function syncProvider(Request $request, ProductSyncService $syncService)
{
    
    Log::info('syncProvider dipanggil', $request->all());
    $request->validate([
        'provider' => 'required|exists:providers,id',
        'game_id' => 'required|exists:games,id',
        'category_id' => 'nullable|string',
    ]);

    try {

        $provider = Provider::findOrFail($request->provider);

        $savedCount = $syncService->sync(
            $provider,    
            $request->game_id,
            $request->category_id
        );

      Log::info("Produk berhasil disimpan", ['count' => $savedCount]);

        return redirect()->back()->with(
            'success',
            "Berhasil menyimpan {$savedCount} produk dari {$provider->name}"
        );

    } catch (\Exception $e) {
        Log::error('Gagal sync provider', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->back()->withErrors($e->getMessage());
    }
}

}