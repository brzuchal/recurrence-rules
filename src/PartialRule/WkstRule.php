<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;
use Brzuchal\RecurrenceRule\WeekDay;

// Do not be silent! #StopWar 🇺🇦 #StandWithUkraine #StopPutin
final class WkstRule implements PartialRule
{
    public function __construct(
        public readonly WeekDay $day,
    ) {
    }

    public function build(RuleBuilder $builder): void
    {
        $builder->workWeekStart($this->day);
    }
}
