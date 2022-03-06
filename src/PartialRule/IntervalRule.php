<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;

// Do not be silent! #StopWar 🇺🇦 #StandWithUkraine #StopPutin
final class IntervalRule implements PartialRule
{
    /**
     * @psalm-param positive-int $interval
     */
    public function __construct(
        public readonly int $interval,
    ) {
    }

    public function build(RuleBuilder $builder): void
    {
        $builder->interval($this->interval);
    }
}
