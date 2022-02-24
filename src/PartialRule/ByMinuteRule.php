<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;

final class ByMinuteRule implements PartialRule
{
    /**
     * @psalm-param list<positive-int> $minList
     */
    public function __construct(
        public readonly array $minList,
    ) {}

    public function build(RuleBuilder $builder): void
    {
        $builder->byMinute($this->minList);
    }
}
