<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsApiResponse extends Model
{
    protected $table = 'newsapi_responses';
    
    protected $fillable = ['keyword', 'response'];

    protected $casts = [
        'response' => 'array',
    ];
}