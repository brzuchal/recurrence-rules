<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

enum WeekDay: string
{
    case Sunday = 'SU';
    case Monday = 'MO';
    case Tuesday = 'TU';
    case Wednesday = 'WE';
    case Thursday = 'TH';
    case Friday = 'FR';
    case Saturday = 'SA';
}
