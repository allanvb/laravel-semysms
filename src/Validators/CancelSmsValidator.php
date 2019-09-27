<?php


namespace Allanvb\LaravelSemysms\Validators;


use Illuminate\Support\Facades\Validator;

class CancelSmsValidator extends SemySmsValidator
{
    /**
     * @return array|mixed
     */
    protected function rules()
    {
        return [
            'device_id' => 'numeric|digits_between:1,10',
            'sms_id' => 'numeric',
        ];
    }
}
