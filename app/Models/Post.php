<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'main_image',
        'short_description',
        'content',
        'gallery_images',
        'author',
    ];

    protected $casts = [
        'gallery_images' => 'array',
    ];
}
