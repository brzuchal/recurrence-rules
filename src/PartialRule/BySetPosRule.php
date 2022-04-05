<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;
use Brzuchal\RecurrenceRule\ValueObject\YearDayNum;

// TODO: investigate if it's worth of replacing YearDayNum with dedicated VO
final class BySetPosRule implements PartialRule
{
    /**
     * @psalm-param list<YearDayNum> $spList
     */
    public function __construct(
        public readonly array $spList,
    ) {
    }

    public function build(RuleBuilder $builder): void
    {
        $builder->bySetPos($this->spList);
    }
}
