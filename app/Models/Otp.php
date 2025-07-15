<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Otp extends Model
{
    protected $fillable = ['phone', 'otp_code', 'expires_at'];

    public function isExpired()
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }
}

