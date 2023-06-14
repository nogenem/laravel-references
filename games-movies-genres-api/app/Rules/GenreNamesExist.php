<?php

namespace App\Rules;

use App\Models\Genre;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class GenreNamesExist implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $genresNames = array_map(
            fn ($name) => strtolower($name),
            $value
        );
        $genresNames = array_unique($genresNames);

        $genresCount = Genre::query()
            ->whereIn(DB::raw('LOWER(name)'), $genresNames)
            ->count();

        if ($genresCount != count($genresNames)) {
            $fail('validation.one_or_more_dont_exist')->translate([
                'models' => __('genres'),
            ]);
        }
    }
}
