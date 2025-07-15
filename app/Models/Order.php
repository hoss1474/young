<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'campaign_id',
        'quantity',
        'price',
        'color',
        'tracking_code',
        'address',
        'status',
        'address_id', // اضافه کن اگر تو جدول هست
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'user_id', 'id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
