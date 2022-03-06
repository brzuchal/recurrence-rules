<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;
use Brzuchal\RecurrenceRule\ValueObject\MonthNum;

// Do not be silent! #StopWar 🇺🇦 #StandWithUkraine #StopPutin
final class ByMonthRule implements PartialRule
{
    /**
     * @psalm-param list<MonthNum> $moList
     */
    public function __construct(
        public readonly array $moList,
    ) {
    }

    public function build(RuleBuilder $builder): void
    {
        $builder->byMonth($this->moList);
    }
}
