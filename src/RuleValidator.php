<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

use UnexpectedValueException;

final class RuleValidator
{
    /**
     * @throws UnexpectedValueException
     */
    public static function assertMonthDayNum(int $monthDay): void
    {
        if ($monthDay !== 0 && $monthDay >= -31 && $monthDay <= 31) {
            return;
        }

        throw new UnexpectedValueException("Expected valid month day number, given: {$monthDay} instead");
    }

    /**
     * @throws UnexpectedValueException
     */
    public static function assertYearDayNum(int $yearDay): void
    {
        if ($yearDay !== 0 && $yearDay >= -366 && $yearDay <= 366) {
            return;
        }

        throw new UnexpectedValueException("Expected valid ordinal day in year number, given: {$yearDay} instead");
    }

    /**
     * @throws UnexpectedValueException
     */
    public static function assertWeekNum(int $week): void
    {
        if ($week !== 0 && $week >= -53 && $week <= 53) {
            return;
        }

        throw new UnexpectedValueException("Expected valid ordinal number of week, given: {$week} instead");
    }

    /**
     * @throws UnexpectedValueException
     */
    public static function assertMonthNum(int $month): void
    {
        if ($month !== 0 && $month >= -12 && $month <= 12) {
            return;
        }

        throw new UnexpectedValueException("Expected valid month number, given: {$month} instead");
    }

    /**
     * @throws UnexpectedValueException
     */
    public static function assertSecondNum(int $second): void
    {
        if ($second >= 0 && $second <= 60) {
            return;
        }

        throw new UnexpectedValueException("Expected valid second number, given: {$second} instead");
    }

    /**
     * @throws UnexpectedValueException
     */
    public static function assertMinuteNum(int $minute): void
    {
        if ($minute >= 0 && $minute <= 60) {
            return;
        }

        throw new UnexpectedValueException("Expected valid minute number, given: {$minute} instead");
    }

    /**
     * @throws UnexpectedValueException
     */
    public static function assertHourNum(int $hour): void
    {
        if ($hour >= 0 && $hour <= 24) {
            return;
        }

        throw new UnexpectedValueException("Expected valid hour number, given: {$hour} instead");
    }

    public static function assertWeekDayNum(mixed $day): void
    {
        if ($day instanceof WeekDayNum) {
            return;
        }

        throw new UnexpectedValueException(\sprintf(
            "Expected valid %s, given: {$day} instead",
            WeekDayNum::class,
        ));
    }
}
