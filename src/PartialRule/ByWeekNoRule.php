<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;
use Brzuchal\RecurrenceRule\ValueObject\WeekNum;

final class ByWeekNoRule implements PartialRule
{
    /**
     * @psalm-param list<WeekNum> $wnList
     */
    public function __construct(
        public readonly array $wnList,
    ) {
    }

    public function build(RuleBuilder $builder): void
    {
        $builder->byWeekNo($this->wnList);
    }
}
