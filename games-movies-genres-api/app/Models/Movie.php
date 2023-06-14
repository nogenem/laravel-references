<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'director',
        'cover_image_url',
        'release_date',
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'genre_movie')->using(GenreMovie::class);
    }
}
