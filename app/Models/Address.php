<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'client_id', 'title', 'address', 'city', 'post_code',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
