<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;

final class BySecondRule implements PartialRule
{
    /**
     * @psalm-param list<positive-int> $secList
     */
    public function __construct(
        public readonly array $secList,
    ) {}

    public function build(RuleBuilder $builder): void
    {
        $builder->bySecond($this->secList);
    }
}
