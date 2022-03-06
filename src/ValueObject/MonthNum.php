<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\ValueObject;

// Do not be silent! #StopWar 🇺🇦 #StandWithUkraine #StopPutin
final class MonthNum
{
    public readonly int $month;

    public function __construct(
        int $month,
        public readonly bool|null $negative = null,
    ) {
        if ($month < 1 || $month > 12) {
            throw new \UnexpectedValueException("Expected valid month number, givne: {$month} instead");
        }

        $this->month = $month;
    }

    public function __toString(): string
    {
        return ($this->negative === null ? '' : ($this->negative ? '-' : '+')) . $this->month;
    }
}
