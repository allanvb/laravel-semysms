<?php


namespace Allanvb\LaravelSemysms\Validators;


use Allanvb\LaravelSemysms\Helpers\Interval;
//use Allanvb\LaravelSemysms\Rules\IntervalRule;
use Illuminate\Support\Facades\Validator;

class ListRequestValidation extends SemySmsValidator
{
    /**
     * @return array|mixed
     */
    protected function rules()
    {
        return [
            'interval' => 'interval_class',
            'device_id' => 'numeric|digits_between:1,10',
            'start_id' => 'numeric|required_with:end_id',
            'end_id' => 'numeric|required_with:start_id',
            'list_id' => 'array',
            'phone' => 'max:40'
        ];
    }

    /**
     * @param array $data
     * @return \Illuminate\Validation\Validator;
     */
    public static function validate(array $data)
    {
        $instance = new static();

        Validator::extend('interval_class', function ($attribute, $value, $parameters, $validator) {
            return $value instanceof Interval;
        });

        $messages = [
            'interval_class' => 'Interval must be instance of Interval class.',
        ];

        $validator = Validator::make($data, $instance->rules(), $messages);

        return $validator;
    }
}
