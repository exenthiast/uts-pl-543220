<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'published_year',
        'stock',
        'description',
    ];

    protected $casts = [
        'published_year' => 'integer',
        'stock' => 'integer',
    ];
}
