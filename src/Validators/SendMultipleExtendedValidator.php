<?php


namespace Allanvb\LaravelSemysms\Validators;


use Illuminate\Support\Facades\Validator;

class SendMultipleExtendedValidator extends SemySmsValidator
{
    /**
     * @return array|mixed
     */
    protected function rules()
    {
        return [
            'to' => 'required|string|max:2|regex:/^\+\d+$/',
            'text' => 'required|max:255',
            'device_id' => 'numeric|digits_between:1,10',
            'my_id' => 'max:50'
        ];
    }
}
