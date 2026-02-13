<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Cache;

class GopayGameScraper
{
    protected Client $client;

    // URL target 2 game
    protected array $gamesUrls = [
        'mobile_legends' => 'https://gopay.co.id/games/mobile-legends-bang-bang',
        'free_fire' => 'https://gopay.co.id/games/free-fire',
    ];

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (compatible; LaravelScraper/1.0)',
            ],
            'timeout' => 10,
        ]);
    }

    /**
     * Ambil data 2 game
     */
    public function getGames(): array
    {
        return Cache::remember('gopaygame_two', now()->addMinutes(30), function () {
            $results = [];

            foreach ($this->gamesUrls as $key => $url) {
                try {
                    $response = $this->client->get($url);
                    $html = (string) $response->getBody();
                    $crawler = new Crawler($html);

                    // Contoh: ambil game ID dari meta tag
                    $id = $crawler->filter('meta[name="game-id"]')->attr('content') ?? null;
                    $name = $crawler->filter('.game-title')->text() ?? ucfirst(str_replace('_',' ',$key));

                    $results[$key] = [
                        'name' => $name,
                        'id' => $id,
                        'url' => $url,
                    ];
                } catch (\Exception $e) {
                    \Log::error("Error fetching $key: ".$e->getMessage());
                    $results[$key] = [
                        'name' => ucfirst(str_replace('_',' ',$key)),
                        'id' => null,
                        'url' => $url,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return $results;
        });
    }
  public function getNickname(string $gameKey, string $userId, ?string $serverId = null): string
{
    // → Perbaikan: normalize gameKey
    $gameCode = strtoupper(str_replace(['-', '_'], '', $gameKey));

    // Pastikan zoneId selalu string
    $zoneId = $serverId ?? '';

    // URL GoPay
    $url = "https://gopay.co.id/games/v1/order/prepare/{$gameCode}?userId={$userId}&zoneId={$zoneId}";

    // ===== Debug log =====
    \Log::info("GoPay check URL: {$url}");
    \Log::info("UserID: {$userId}, ZoneID: {$zoneId}");

    // Request ke GoPay
    $response = $this->client->get($url);
    $json = json_decode((string) $response->getBody(), true);

    if (!isset($json['data']) || empty($json['data'])) {
        throw new \Exception("User ID tidak valid");
    }

    return $json['data']; // nickname
}

}
