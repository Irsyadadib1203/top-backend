<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nominal extends Model
{
    protected $table = 'nominals';

    protected $fillable = [
        'id',
        'game_id',
        'provider_id',
        'name',
        'description',
        'base_price',
        'selling_price',
        'margin_percent',
        'provider_product_code',
        'is_active',
        'sort_order',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
