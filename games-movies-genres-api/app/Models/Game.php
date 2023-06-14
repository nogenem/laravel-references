<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'developer',
        'cover_image_url',
        'release_date',
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'game_genre')->using(GameGenre::class);
    }
}
