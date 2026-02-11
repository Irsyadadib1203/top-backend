<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminProfile extends Model
{
    protected $table = 'admin_profiles';

    protected $fillable = [
        'user_id',
        'full_name',
        'avatar_url',
    ];

    protected $keyType = 'string';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
