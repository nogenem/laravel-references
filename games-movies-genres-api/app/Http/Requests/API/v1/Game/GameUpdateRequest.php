<?php

namespace App\Http\Requests\API\v1\Game;

use App\Rules\GenreNamesExist;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GameUpdateRequest extends FormRequest
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
        $id = $this->route('game');
        $name = strtolower($this->name);

        $isRequired = !$this->isMethod('PATCH');

        return [
            'name' => [
                $isRequired ? 'required' : '',
                'string',
                'max:255',
                Rule::unique('games')->where(function (Builder $query) use ($name) {
                    $query->where(DB::raw('LOWER(name)'), 'LIKE', "%$name%");
                })->ignore($id)
            ],
            'developer' => [$isRequired ? 'required' : '', 'string', 'max:255'],
            'cover_image_url' => [$isRequired ? 'required' : '', 'url', 'max:255'],
            'release_date' => [$isRequired ? 'required' : '', 'date'],
            'genres' => [
                $isRequired ? 'required' : '',
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
                'model' => __('game'),
                'attribute' => __('name')
            ]),
        ];
    }
}
