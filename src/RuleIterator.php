<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

use DateTime;
use DateTimeImmutable;
use Generator;
use IteratorAggregate;
use RRule\RRule;
use Traversable;

/**
 * @implements IteratorAggregate<int, DateTimeImmutable>
 */
final class RuleIterator implements IteratorAggregate
{
    public function __construct(
        public readonly DateTimeImmutable $start,
        public readonly Rule $rule,
    ) {
    }

    /**
     * @psalm-return Generator<int, DateTimeImmutable>
     */
    public function getIterator(): Traversable
    {
        foreach (new RRule($this->toParts() + ['DTSTART' => $this->start]) as $occurence => $date) {
            \assert(\is_int($occurence));
            \assert($date instanceof DateTime);

            yield $occurence => DateTimeImmutable::createFromMutable($date);
        }
    }

    /**
     * @psalm-return array<string, int|string>
     */
    protected function toParts(): array
    {
        $parts = ['FREQ' => $this->rule->freq->value];
        if ($this->rule->until) {
            $parts['UNTIL'] = $this->rule->until->format('Ymd\THis');
        }

        if ($this->rule->count) {
            $parts['COUNT'] = $this->rule->count;
        }

        if ($this->rule->interval) {
            $parts['INTERVAL'] = $this->rule->interval;
        }

        if ($this->rule->bySecond) {
            $parts['BYSECOND'] = \implode(',', $this->rule->bySecond);
        }

        if ($this->rule->byMinute) {
            $parts['BYMINUTE'] = \implode(',', $this->rule->byMinute);
        }

        if ($this->rule->byHour) {
            $parts['BYHOUR'] = \implode(',', $this->rule->byHour);
        }

        if ($this->rule->byDay) {
            $parts['BYDAY'] = \implode(',', $this->rule->byDay);
        }

        if ($this->rule->byMonthDay) {
            $parts['BYMONTHDAY'] = \implode(',', $this->rule->byMonthDay);
        }

        if ($this->rule->byYearDay) {
            $parts['BYYEARDAY'] = \implode(',', $this->rule->byYearDay);
        }

        if ($this->rule->byWeekNo) {
            $parts['BYWEEKNO'] = \implode(',', $this->rule->byWeekNo);
        }

        if ($this->rule->byMonth) {
            $parts['BYMONTH'] = \implode(',', $this->rule->byMonth);
        }

        if ($this->rule->bySetPos) {
            $parts['BYSETPOS'] = \implode(',', $this->rule->bySetPos);
        }

        if ($this->rule->workWeekStart) {
            $parts['WKST'] = $this->rule->workWeekStart->value;
        }

        return $parts;
    }
}
