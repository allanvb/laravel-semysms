<?php


namespace Allanvb\LaravelSemysms\Validators;


use Illuminate\Support\Facades\Validator;

class SendMultipleValidator extends SemySmsValidator
{
    /**
     * @return array|mixed
     */
    protected function rules()
    {
        return [
            'to' => 'required|array',
            'to.*' => 'max:30|regex:/^\+\d+$/',
            'text' => 'required|max:255'
        ];
    }
}
