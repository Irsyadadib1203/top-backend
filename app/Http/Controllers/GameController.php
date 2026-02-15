<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Goutte\Client;
use App\Models\GameProvider;
use App\Services\Provider\ProviderManager;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.produk.game', compact('games'));
    }

    public function store(Request $request,ProviderManager $providerManager)
    {
        
        $request->validate([
            'name'        => 'required|string|max:100',
            'category'    => 'required|string|max:50',
            'description' => 'nullable|string',
            'image_url'       => 'nullable|string|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image_url')) {
            $imagePath = $request->file('image_url')->store('games', 'public');
        }

        $game =Game::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'category'    => $request->category,
            'description' => $request->description,
            'image_url'   => $request->image_url,
            'is_active'   => $request->has('is_active'),
            'is_popular'  => $request->has('is_popular'),
        ]);

        // 2️⃣ Ambil kategori dari provider otomatis
    $providers = ['ffz', 'digiflazz']; // daftar provider

    foreach ($providers as $providerCode) {
        $provider = $providerManager->driver($providerCode);

        $categoryId = null;

        if (method_exists($provider, 'getCategories')) {
            $categories = $provider->getCategories();

            // ambil kategori yang sesuai nama game, fallback ke index 0
            $matched = collect($categories)->firstWhere('name', $game->name);

            $categoryId = $matched['id'] ?? ($categories[0]['id'] ?? null);
        }

        // 3️⃣ Simpan mapping ke game_providers
        if ($categoryId) {
            GameProvider::create([
                'game_id' => $game->id,
                'provider_code' => strtoupper($providerCode),
                'provider_category_id' => $categoryId,
            ]);
        }
    }

    return redirect()->back()->with('success', 'Game berhasil ditambahkan & mapping provider otomatis dibuat');
    }

    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);
        

        $request->validate([
            'name'        => 'required|string|max:100',
            'category'    => 'required|string|max:50',
            'description' => 'nullable|string',
            'image_url'       => 'nullable|string|max:2048',
        ]);

        if ($request->hasFile('image_url')) {
            $game->image_url = $request->file('image_url')->store('games', 'public');
        }

        $game->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'image_url'   => $request->image_url,
            'category'    => $request->category,
            'description' => $request->description,
            'is_active'   => $request->has('is_active'),
            'is_popular'  => $request->has('is_popular'),
        ]);

        return redirect()->back()->with('success', 'Game berhasil diperbarui');
    }

    public function destroy($id)
    {
        Game::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Game berhasil dihapus');
    }
    public function checkGameId(Request $request)
    {
        $request->validate([
            'game_id' => 'required|string',  // ID yang diinput user
        ]);

        $client = new Client();
        $url = 'https://gopay.co.id/games';  // URL GoPay

        try {
            $crawler = $client->request('GET', $url);

            // Cari game berdasarkan ID (sesuaikan selector berdasarkan HTML GoPay)
            $gameElement = $crawler->filter("[data-game-id='{$request->game_id}']");  // Asumsikan ada attribute data-game-id

            if ($gameElement->count() > 0) {
                $name = $gameElement->filter('.game-name')->text();  // Ganti selector nyata
                $image = $gameElement->filter('img')->attr('src');
                $category = $gameElement->filter('.game-category')->text();

                // Simpan atau return data
                $game = Game::updateOrCreate(
                    ['slug' => \Str::slug($name)],  // Asumsikan slug unik
                    [
                        'name' => $name,
                        'image' => $image,
                        'category' => $category,
                        'description' => 'Checked from GoPay',
                        'is_active' => true,
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Game ditemukan dan disimpan.',
                    'data' => $game,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Game ID tidak ditemukan di GoPay.',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
