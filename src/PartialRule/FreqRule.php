<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\PartialRule;

use Brzuchal\RecurrenceRule\Freq;
use Brzuchal\RecurrenceRule\PartialRule;
use Brzuchal\RecurrenceRule\RuleBuilder;

final class FreqRule implements PartialRule
{
    public function __construct(
        public readonly Freq $freq,
    ) {}

    public function build(RuleBuilder $builder): void
    {
        $builder->freq($this->freq);
    }
}
