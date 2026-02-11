<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; 

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /* ======================
     |  RELATIONS
     | ====================== */

    public function adminProfile()
    {
        return $this->hasOne(AdminProfile::class);
    }

    public function adminRole()
    {
        return $this->hasOne(AdminRole::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'customer_id');
    }

    /* ======================
     |  HELPERS
     | ====================== */

    public function isAdmin(): bool
    {
        return optional($this->adminRole)->role === 'admin';
    }

    public function isSuperadmin(): bool
    {
        return optional($this->adminRole)->role === 'superadmin';
    }

    public function isOperator(): bool
    {
        return optional($this->adminRole)->role === 'operator';
    }
}
