<?php

namespace App\Http\Requests\API\v1\Movie;

use App\Rules\GenreNamesExist;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MovieStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $name = strtolower($this->name);
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('movies')->where(function (Builder $query) use ($name) {
                    $query->where(DB::raw('LOWER(name)'), 'LIKE', "%$name%");
                })
            ],
            'director' => ['required', 'string', 'max:255'],
            'cover_image_url' => ['required', 'url', 'max:255'],
            'release_date' => ['required', 'date'],
            'genres' => [
                'required',
                'array',
                'min:1',
                new GenreNamesExist()
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('validation.already_exists', [
                'model' => __('movie'),
                'attribute' => __('name')
            ]),
        ];
    }
}
