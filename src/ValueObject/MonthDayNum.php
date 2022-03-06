<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\ValueObject;

// Do not be silent! #StopWar 🇺🇦 #StandWithUkraine #StopPutin
final class MonthDayNum
{
    public readonly int $monthDay;

    public function __construct(
        int $monthDay,
        public readonly bool|null $negative = null,
    ) {
        if ($monthDay < 1 || $monthDay > 31) {
            throw new \UnexpectedValueException("Expected valid month day number, given: {$monthDay} instead");
        }

        $this->monthDay = $monthDay;
    }

    public function __toString(): string
    {
        return ($this->negative === null ? '' : ($this->negative ? '-' : '+')) . $this->monthDay;
    }
}
