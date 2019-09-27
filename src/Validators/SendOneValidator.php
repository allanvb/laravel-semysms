<?php


namespace Allanvb\LaravelSemysms\Validators;


use Illuminate\Support\Facades\Validator;

class SendOneValidator extends SemySmsValidator
{
    /**
     * @return array|mixed
     */
    protected function rules()
    {
        return [
            'to' => 'required|string|max:30|regex:/^\+\d+$/',
            'text' => 'required|max:255',
        ];
    }

    /**
     * @param array $data
     * @return \Illuminate\Validation\Validator
     */
    public static function validate(array $data)
    {
        $instance = new static();

        $validator = Validator::make($data, $instance->rules());

        $validator->sometimes('device_id', 'digits_between:1,10', function ($input) {
            return is_numeric($input->device_id);
        });

        $validator->sometimes('device_id', 'in:active', function ($input) {
            return !is_numeric($input->device_id);
        });

        return $validator;
    }
}
