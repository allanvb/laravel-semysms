<?php

namespace Allanvb\LaravelSemysms\Exceptions;

use Exception;

class SmsNotSentException extends Exception
{
    /**
     * @param mixed $errorCode
     * @param mixed $message
     * @return SmsNotSentException
     */
    public static function create($errorCode, $message)
    {
        return new static('Sms not sent. Error code: ' . $errorCode . ', Message: ' . $message);
    }

}
