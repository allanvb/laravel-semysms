<?php


namespace Allanvb\LaravelSemysms\Validators;


use Illuminate\Support\Facades\Validator;

abstract class SemySmsValidator
{
    /**
     * @return mixed
     */
    protected abstract function rules();

    /**
     * @param array $data
     * @return \Illuminate\Validation\Validator
     */
    public static function validate(array $data)
    {
        $instance = new static();

        return Validator::make($data, $instance->rules());
    }
}
