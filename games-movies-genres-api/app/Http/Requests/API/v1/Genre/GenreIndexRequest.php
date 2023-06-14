<?php

namespace App\Http\Requests\API\v1\Genre;

use Illuminate\Foundation\Http\FormRequest;

class GenreIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function validationData()
    {
        return $this->route()->parameters() + $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'per_page' => ['numeric', 'min:1']
        ];
    }
}
