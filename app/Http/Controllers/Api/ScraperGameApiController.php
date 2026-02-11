<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GoPayGameScraper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScraperGameApiController extends Controller
{
    protected GoPayGameScraper $scraper;

    public function __construct(GoPayGameScraper $scraper)
    {
        $this->scraper = $scraper;
    }

    public function twoGames(): JsonResponse
    {
        $games = $this->scraper->getGames();

        return response()->json([
            'status' => 'success',
            'data' => $games,
        ]);
    }
   public function checkUser(Request $request, $game)
{
    $gameKey = str_replace('-', '_', $game); // ubah free-fire → free_fire

    $userId = $request->query('userId');
    $serverId = $request->query('serverId');

    try {
        $nickname = $this->scraper->getNickname($gameKey, $userId, $serverId);

        return response()->json([
            'message' => 'Success',
            'data' => $nickname,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'User ID tidak valid'
        ], 400);
    }
}


}
