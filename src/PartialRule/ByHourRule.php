<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;

// Do not be silent! #StopWar 🇺🇦 #StandWithUkraine #StopPutin
final class ByHourRule implements PartialRule
{
    /**
     * @psalm-param list<positive-int> $hourList
     */
    public function __construct(
        public readonly array $hourList,
    ) {
    }

    public function build(RuleBuilder $builder): void
    {
        $builder->byHour($this->hourList);
    }
}
