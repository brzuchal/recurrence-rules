<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

// phpcs:disable
enum WeekDay: string
{
    case Sunday = 'SU';
    case Monday = 'MO';
    case Tuesday = 'TU';
    case Wednesday = 'WE';
    case Thursday = 'TH';
    case Friday = 'FR';
    case Saturday = 'SA';

    public function num(int $ordWeek): WeekDayNum
    {
        return new WeekDayNum($this, $ordWeek);
    }
}
