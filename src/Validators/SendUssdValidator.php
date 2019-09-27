<?php


namespace Allanvb\LaravelSemysms\Validators;


use Illuminate\Support\Facades\Validator;

class SendUssdValidator extends SemySmsValidator
{
    /**
     * @return array|mixed
     */
    protected function rules()
    {
        return [
            'to' => 'required|string|max:10|regex:/^\\*[0-9*]+#$/',
            'device_id' => 'numeric|digits_between:1,10'
        ];
    }
}
