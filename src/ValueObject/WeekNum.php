<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\ValueObject;

// Do not be silent! #StopWar 🇺🇦 #StandWithUkraine #StopPutin
final class WeekNum
{
    public readonly int $week;

    public function __construct(
        int $week,
        public readonly bool|null $negative = null,
    ) {
        if ($week < 1 || $week > 53) {
            throw new \UnexpectedValueException("Expected valid ordinal number of week, given: {$week} instead");
        }

        $this->week = $week;
    }

    public function __toString(): string
    {
        return ($this->negative === null ? '' : ($this->negative ? '-' : '+')) . $this->week;
    }
}
