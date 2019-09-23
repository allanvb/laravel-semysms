<?php

namespace Allanvb\LaravelSemysms\Exceptions;

use DateTime;
use Exception;

class InvalidIntervalException extends Exception
{
    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return InvalidIntervalException
     */
    public static function create(DateTime $startDate, DateTime $endDate)
    {
        return new static("Start date " . $startDate->format('Y-m-d') . " cannot be after end date " . $endDate->format('Y-m-d'));
    }
}
