<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

use UnexpectedValueException;

final class WeekDayNum
{
    // phpcs:ignore
    public readonly int|null $ordWeek;

    public function __construct(
        public readonly WeekDay $weekDay,
        // phpcs:ignore
        int|null $ordWeek = null,
    ) {
        if ($ordWeek !== null && ($ordWeek === 0 || $ordWeek < -53 || $ordWeek > 53)) {
            throw new UnexpectedValueException("Expected ordinal number of week, given: {$ordWeek} instead");
        }

        $this->ordWeek = $ordWeek;
    }

    public function __toString(): string
    {
        if ($this->ordWeek === null) {
            return $this->weekDay->value;
        }

        return $this->ordWeek . $this->weekDay->value;
    }
}
