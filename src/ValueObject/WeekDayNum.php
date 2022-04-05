<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\ValueObject;

use Brzuchal\RecurrenceRule\WeekDay;

final class WeekDayNum
{
    public readonly int | null $ordWeek;

    public function __construct(
        public readonly WeekDay $weekDay,
        int|null $ordWeek = null,
        public readonly bool|null $negative = null,
    ) {
        if ($ordWeek !== null && ($ordWeek < 1 || $ordWeek > 53)) {
            throw new \UnexpectedValueException("Expected ordinal number of week, given: {$ordWeek} instead");
        }

        $this->ordWeek = $ordWeek;
    }

    public function __toString(): string
    {
        return ($this->negative === null ? '' : ($this->negative ? '-' : '+')) . $this->ordWeek . $this->weekDay->value;
    }
}
