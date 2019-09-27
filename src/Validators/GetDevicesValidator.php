<?php


namespace Allanvb\LaravelSemysms\Validators;


use Illuminate\Support\Facades\Validator;

class GetDevicesValidator extends SemySmsValidator
{
    /**
     * @return array|mixed
     */
    protected function rules()
    {
        return [
            'status' => 'in:active,archived',
            'list_id' => 'array'
        ];
    }
}
