<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'clients';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'password' ,'profile_image',
    ];

    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // ✅ رابطه جدید: هر کلاینت چند آدرس دارد
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
