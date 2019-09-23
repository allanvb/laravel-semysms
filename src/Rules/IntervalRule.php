<?php

namespace Allanvb\LaravelSemysms\Rules;

use Allanvb\LaravelSemysms\Helpers\Interval;
use Illuminate\Contracts\Validation\Rule;

class IntervalRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return $value instanceof Interval;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Interval must be instance of Interval class';
    }
}
