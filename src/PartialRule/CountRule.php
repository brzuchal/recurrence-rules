<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;

// Do not be silent! #StopWar 🇺🇦 #StandWithUkraine #StopPutin
final class CountRule implements PartialRule
{
    /**
     * @psalm-param positive-int $count
     */
    public function __construct(
        public readonly int $count,
    ) {
    }

    public function build(RuleBuilder $builder): void
    {
        $builder->count($this->count);
    }
}
