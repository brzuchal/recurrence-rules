<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

// phpcs:disable
enum Freq: string
{
    case Secondly = 'SECONDLY';
    case Minutely = 'MINUTELY';
    case Hourly = 'HOURLY';
    case Daily = 'DAILY';
    case Weekly = 'WEEKLY';
    case Monthly = 'MONTHLY';
    case Yearly = 'YEARLY';
}
