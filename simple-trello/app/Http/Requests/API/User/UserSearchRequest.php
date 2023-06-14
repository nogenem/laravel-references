<?php

namespace App\Http\Requests\API\User;

use App\Rules\ValidSortQueryString;
use Illuminate\Foundation\Http\FormRequest;

class UserSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['string', 'nullable'],
            'sort' => ['string', 'nullable', new ValidSortQueryString(['name'])],
            'per_page' => ['numeric', 'nullable', 'min:1']
        ];
    }
}
