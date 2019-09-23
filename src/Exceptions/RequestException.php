<?php


namespace Allanvb\LaravelSemysms\Exceptions;

use Exception;

class RequestException extends Exception
{
    /**
     * @param mixed $statusCode
     * @return RequestException
     */
    public static function create($statusCode)
    {
        return new static('Request error. HTTP Response: ' . $statusCode);
    }
}
