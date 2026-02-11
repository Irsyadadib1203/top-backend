<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'games';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'image_url',
        'description',
        'is_active',
        'is_popular',
        'category',
        'sort_order',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function nominals()
    {
        return $this->hasMany(Nominal::class);
    }
}
