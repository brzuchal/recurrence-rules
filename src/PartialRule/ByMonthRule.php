<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;
use Brzuchal\RecurrenceRule\ValueObject\MonthNum;

final class ByMonthRule implements PartialRule
{
    /**
     * @psalm-param list<MonthNum> $moList
     */
    public function __construct(
        public readonly array $moList,
    ) {}

    public function build(RuleBuilder $builder): void
    {
        $builder->byMonth($this->moList);
    }
}
