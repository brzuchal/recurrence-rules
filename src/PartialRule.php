<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

interface PartialRule
{
    public function build(RuleBuilder $builder): void;
}
