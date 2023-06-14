<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidSortQueryString implements Rule
{
    private $validValues;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($validValues)
    {
        $this->validValues = $validValues;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = $value ?? '';
        if (str_starts_with($value, "-")) {
            $value = \substr($value, 1);
        } elseif (str_starts_with($value, "+")) {
            $value = \substr($value, 1);
        }
        return empty($value) || \in_array($value, $this->validValues);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The selected :attribute is invalid.';
    }
}
