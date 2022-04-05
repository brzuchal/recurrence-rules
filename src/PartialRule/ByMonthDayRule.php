<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;
use Brzuchal\RecurrenceRule\ValueObject\MonthDayNum;

final class ByMonthDayRule implements PartialRule
{
    /**
     * @psalm-param list<MonthDayNum> $monthDayList
     */
    public function __construct(
        public readonly array $monthDayList,
    ) {
    }

    public function build(RuleBuilder $builder): void
    {
        $builder->byMonthDay($this->monthDayList);
    }
}
