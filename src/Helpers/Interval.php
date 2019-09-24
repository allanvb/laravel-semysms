<?php

namespace Allanvb\LaravelSemysms\Helpers;

use DateTime;
use Carbon\Carbon;
use Allanvb\LaravelSemysms\Exceptions\InvalidIntervalException;

class Interval
{
    /**
     * @var DateTime
     */
    public $startDate;

    /**
     * @var DateTime
     */
    public $endDate;

    /**
     * Interval constructor.
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @throws InvalidIntervalException
     */
    public function __construct(DateTime $startDate, DateTime $endDate)
    {
        if ($startDate > $endDate) {
            throw InvalidIntervalException::create($startDate, $endDate);
        }

        $this->startDate = $startDate->format('Y-m-d H:i:s');
        $this->endDate = $endDate->format('Y-m-d H:i:s');
    }

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return Interval
     * @throws InvalidIntervalException
     */
    public static function create(DateTime $startDate, DateTime $endDate): self
    {
        return new static($startDate, $endDate);
    }

    /**
     * @param int $numberOfHours
     * @return Interval
     * @throws InvalidIntervalException
     */
    public static function hours(int $numberOfHours): self
    {
        $endDate = Carbon::now();

        $startDate = Carbon::now()->subHours($numberOfHours);

        return new static($startDate, $endDate);
    }

    /**
     * @param int $numberOfDays
     * @return Interval
     * @throws InvalidIntervalException
     */
    public static function days(int $numberOfDays): self
    {
        $endDate = Carbon::today()->endOfDay();

        $startDate = Carbon::today()->subDays($numberOfDays)->startOfDay();

        return new static($startDate, $endDate);
    }

    /**
     * @param int $numberOfWeeks
     * @return Interval
     * @throws InvalidIntervalException
     */
    public static function weeks(int $numberOfWeeks): self
    {
        $endDate = Carbon::today()->endOfDay();

        $startDate = Carbon::today()->subWeeks($numberOfWeeks)->startOfDay();

        return new static($startDate, $endDate);
    }

    /**
     * @param int $numberOfMonths
     * @return Interval
     * @throws InvalidIntervalException
     */
    public static function months(int $numberOfMonths): self
    {
        $endDate = Carbon::today()->endOfDay();

        $startDate = Carbon::today()->subMonths($numberOfMonths)->startOfDay();

        return new static($startDate, $endDate);
    }

    /**
     * @param int $numberOfYears
     * @return Interval
     * @throws InvalidIntervalException
     */
    public static function years(int $numberOfYears): self
    {
        $endDate = Carbon::today()->endOfDay();

        $startDate = Carbon::today()->subYears($numberOfYears)->startOfDay();

        return new static($startDate, $endDate);
    }
}
