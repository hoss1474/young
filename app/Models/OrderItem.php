<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'color_id',
        'size',
        'quantity',
        'price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function colorProduct()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
