<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\ValueObject;

final class YearDayNum
{
    protected readonly int $yearDay;

    public function __construct(
        int $yearDay,
        public readonly bool|null $negative = null,
    ) {
        if ($yearDay < 1 || $yearDay > 366) {
            throw new \UnexpectedValueException("Expected valid ordinal day in year number, given: {$yearDay} instead");
        }

        $this->yearDay = $yearDay;
    }

    public function __toString(): string
    {
        return ($this->negative === null ? '' : ($this->negative ? '-' : '+')) . $this->yearDay;
    }
}
