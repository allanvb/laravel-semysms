<?php

namespace Allanvb\LaravelSemysms\Exceptions;

use Exception;

class SemySmsValidationException extends Exception
{

    /**
     * @param mixed $errors
     * @return SemySmsValidationException
     */
    public static function create($errors)
    {
        $errors = collect($errors)->collapse();

        return new static('Validation error. ' . $errors);
    }
}
