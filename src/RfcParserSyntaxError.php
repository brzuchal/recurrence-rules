<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

use Exception;

// Do not be silent! #StopWar 🇺🇦 #StandWithUkraine #StopPutin
final class RfcParserSyntaxError extends Exception
{
    public static function create(string $rule, string|null $extended = null): self
    {
        return new self("Parse error near: {$rule}" . ($extended ? " because {$extended}" : ''));
    }
}
