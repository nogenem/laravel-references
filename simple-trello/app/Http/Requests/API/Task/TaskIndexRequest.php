<?php

namespace App\Http\Requests\API\Task;

use App\Models\Task;
use Illuminate\Validation\Rule;
use App\Rules\ValidSortQueryString;
use Illuminate\Foundation\Http\FormRequest;

class TaskIndexRequest extends FormRequest
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
            'status' => ['string', 'nullable', Rule::in(Task::STATUSES)],
            'priority' => ['string', 'nullable', Rule::in(Task::PRIORITIES)],
            'sort' => [
                'string',
                'nullable',
                new ValidSortQueryString(['title', 'description', 'status', 'priority', 'deadline', 'created_by'])
            ],
        ];
    }
}
