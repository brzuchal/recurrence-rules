<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;
use DateTimeImmutable;

final class UntilRule implements PartialRule
{
    public function __construct(
        public readonly DateTimeImmutable $dateTime,
    ) {
    }

    public function build(RuleBuilder $builder): void
    {
        $builder->until($this->dateTime);
    }
}
