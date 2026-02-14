<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $table = 'providers';

    protected $fillable = [
        'name',
        'code',
        'api_url',
        'api_key_name',
        'is_active',
    ];

    public $incrementing = true;
    protected $keyType = 'int';

    public function nominals()
    {
        return $this->hasMany(Nominal::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
