<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameProvider extends Model
{
    protected $table = 'game_providers';

    protected $fillable = [
        'game_id',
        'provider_code',
        'provider_category_id',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
