<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class GameApiController extends Controller
{
    /**
     * =========================
     * GET ALL PUBLIC GAMES (GET /api/games)
     * =========================
     */
    public function index()
    {
        $games = Game::with(['nominals' => function ($query) {
            $query->where('is_active', true); // Filter nominals aktif
        }])
        ->where('is_active', true) // Filter games aktif
        ->orderBy('sort_order', 'asc') // Urutkan berdasarkan sort_order jika ada
        ->get();

        $data = $games->map(function ($game) {
            return [
                'id' => $game->id,
                'slug' => $game->slug,
                'name' => $game->name,
                'image' => $game->image_url, // Sesuai interface Next.js (image)
                'category' => $game->category,
                'isPopular' => $game->is_popular, // Sesuai interface Next.js (isPopular)
                'description' => $game->description ?? '',
                'products' => $game->nominals->map(function ($nominal) {
                    return [
                        'id' => $nominal->id,
                        'name' => $nominal->name,
                        'price' => $nominal->selling_price, // Sesuai interface Next.js (price)
                        'originalPrice' => $nominal->base_price, // Sesuai interface Next.js (originalPrice)
                    ];
                }),
            ];
        });

        return response()->json($data);
    }

    /**
     * =========================
     * GET SINGLE GAME BY SLUG (GET /api/games/{slug})
     * =========================
     */
    public function show($slug)
    {
        $game = Game::with(['nominals' => function ($query) {
            $query->where('is_active', true); // Filter nominals aktif
        }])
        ->where('slug', $slug)
        ->where('is_active', true)
        ->first();

        if (!$game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        $data = [
            'id' => $game->id,
            'slug' => $game->slug,
            'name' => $game->name,
            'image' => $game->image_url,
            'category' => $game->category,
            'isPopular' => $game->is_popular,
            'description' => $game->description ?? '',
            'products' => $game->nominals->map(function ($nominal) {
                return [
                    'id' => $nominal->id,
                    'name' => $nominal->name,
                    'price' => $nominal->selling_price,
                    'originalPrice' => $nominal->base_price,
                ];
            }),
        ];

        return response()->json($data);
    }
}