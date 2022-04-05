<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;
use Brzuchal\RecurrenceRule\ValueObject\WeekDayNum;

final class ByDayRule implements PartialRule
{
    /**
     * @psalm-param list<WeekDayNum> $weekDayNumList
     */
    public function __construct(
        public readonly array $weekDayNumList
    ) {
    }

    public function build(RuleBuilder $builder): void
    {
        $builder->byDay($this->weekDayNumList);
    }
}
