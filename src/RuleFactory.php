<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

final class RuleFactory
{
    /**
     * @throws RfcParserSyntaxError
     */
    public function fromString(string $rule): Rule
    {
        $builder = new RuleBuilder();
        foreach (RfcParser::fromString($rule) as $partialRule) {
            $partialRule->build($builder);
        }

        return $builder->build();
    }
}
