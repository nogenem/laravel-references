<?php

namespace App\Http\Requests\API\Task;

use App\Models\Task;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'description' => ['string', 'nullable'],
            'status' => ['string', 'nullable', Rule::in(Task::STATUSES)],
            'priority' => ['string', 'nullable', Rule::in(Task::PRIORITIES)],
            'deadline' => ['date', 'nullable'],
        ];
    }
}
