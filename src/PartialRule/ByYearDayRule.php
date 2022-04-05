<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;
use Brzuchal\RecurrenceRule\ValueObject\YearDayNum;

final class ByYearDayRule implements PartialRule
{
    /**
     * @psalm-param list<YearDayNum> $ydList
     */
    public function __construct(
        public readonly array $ydList,
    ) {
    }

    public function build(RuleBuilder $builder): void
    {
        $builder->byYearDay($this->ydList);
    }
}
