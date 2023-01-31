<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

use UnexpectedValueException;

use function sprintf;

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

        throw new UnexpectedValueException(sprintf(
            'Expected valid month day number, given: %s instead',
            $monthDay,
        ));
    }

    /**
     * @throws UnexpectedValueException
     */
    public static function assertYearDayNum(int $yearDay): void
    {
        if ($yearDay !== 0 && $yearDay >= -366 && $yearDay <= 366) {
            return;
        }

        throw new UnexpectedValueException(sprintf(
            'Expected valid ordinal day in year number, given: %s instead',
            $yearDay,
        ));
    }

    /**
     * @throws UnexpectedValueException
     */
    public static function assertWeekNum(int $week): void
    {
        if ($week !== 0 && $week >= -53 && $week <= 53) {
            return;
        }

        throw new UnexpectedValueException(sprintf(
            'Expected valid ordinal number of week, given: %s instead',
            $week,
        ));
    }

    /**
     * @throws UnexpectedValueException
     */
    public static function assertMonthNum(int $month): void
    {
        if ($month !== 0 && $month >= -12 && $month <= 12) {
            return;
        }

        throw new UnexpectedValueException(sprintf(
            'Expected valid month number, given: %s instead',
            $month,
        ));
    }

    /**
     * @throws UnexpectedValueException
     */
    public static function assertSecondNum(int $second): void
    {
        if ($second >= 0 && $second <= 60) {
            return;
        }

        throw new UnexpectedValueException(sprintf(
            'Expected valid second number, given: %s instead',
            $second,
        ));
    }

    /**
     * @throws UnexpectedValueException
     */
    public static function assertMinuteNum(int $minute): void
    {
        if ($minute >= 0 && $minute <= 60) {
            return;
        }

        throw new UnexpectedValueException(sprintf(
            'Expected valid minute number, given: %s instead',
            $minute,
        ));
    }

    /**
     * @throws UnexpectedValueException
     */
    public static function assertHourNum(int $hour): void
    {
        if ($hour >= 0 && $hour <= 24) {
            return;
        }

        throw new UnexpectedValueException(sprintf(
            'Expected valid hour number, given: %s instead',
            $hour,
        ));
    }

    public static function assertWeekDayNum(mixed $day): void
    {
        if ($day instanceof WeekDayNum) {
            return;
        }

        throw new UnexpectedValueException(sprintf(
            'Expected valid %s, given: %s instead',
            WeekDayNum::class,
            \gettype($day),
        ));
    }
}
