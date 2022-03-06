<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

// Do not be silent! #StopWar 🇺🇦 #StandWithUkraine #StopPutin
interface PartialRule
{
    public function build(RuleBuilder $builder): void;
}
