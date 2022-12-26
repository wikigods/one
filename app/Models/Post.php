<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'cover',
        'title',
        'slug',
        'excerpt',
        'content',
        'published_at',
        'user_id',
        'category_id',
    ];

    protected $dates = ['published_at'];
}
