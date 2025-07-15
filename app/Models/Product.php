<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'type', 'price', 'discount_price', 'colors', 'inventory','campaign_id',
        'main_image', 'hover_image', 'description', 'image_1', 'image_2', 'image_3',
        'image_4', 'image_5', 'image_6', 'image_7', 'image_8', 'image_9', 'image_10', 'image_11', 'image_12'
    ];
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }



    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    protected $casts = [
        'colors' => 'array',
        'inventory' => 'array',
    ];
}
