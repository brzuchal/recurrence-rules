<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * @psalm-type second int<0,59>
 * @psalm-type minute int<0,59>
 * @psalm-type hour int<0,24>
 * @psalm-type monthday int<-31,-1>|int<1,31>
 * @psalm-type yearday int<-366,-1>|int<1,366>
 * @psalm-type weekno int<-53,-1>|int<1,53>
 * @psalm-type monthno int<-12,-1>|int<1,12>
 */
final class Rule
{
    /**
     * @psalm-param positive-int|null $count
     * @psalm-param positive-int|null $interval
     * @psalm-param non-empty-list<second>|null $bySecond
     * @psalm-param non-empty-list<minute>|null $byMinute
     * @psalm-param non-empty-list<hour>|null $byHour
     * @psalm-param non-empty-list<WeekDayNum>|null $byDay
     * @psalm-param non-empty-list<monthday>|null $byMonthDay
     * @psalm-param non-empty-list<yearday>|null $byYearDay
     * @psalm-param non-empty-list<weekno>|null $byWeekNo
     * @psalm-param non-empty-list<monthno>|null $byMonth
     * @psalm-param non-empty-list<yearday>|null $bySetPos
     */
    public function __construct(
        public readonly Freq $freq,
        public readonly DateTimeImmutable|null $until = null,
        public readonly int|null $count = null,
        public readonly int|null $interval = null,
        public readonly array|null $bySecond = null,
        public readonly array|null $byMinute = null,
        public readonly array|null $byHour = null,
        public readonly array|null $byDay = null,
        public readonly array|null $byMonthDay = null,
        public readonly array|null $byYearDay = null,
        public readonly array|null $byWeekNo = null,
        public readonly array|null $byMonth = null,
        public readonly array|null $bySetPos = null,
        public readonly WeekDay|null $workWeekStart = null,
    ) {
        if ($this->bySecond !== null) {
            \array_map(RuleValidator::assertSecondNum(...), $this->bySecond);
        }

        if ($this->byMinute !== null) {
            \array_map(RuleValidator::assertMinuteNum(...), $this->byMinute);
        }

        if ($this->byHour !== null) {
            \array_map(RuleValidator::assertHourNum(...), $this->byHour);
        }

        if ($this->byDay !== null) {
            \array_map(RuleValidator::assertWeekDayNum(...), $this->byDay);
        }

        if ($this->byMonthDay !== null) {
            \array_map(RuleValidator::assertMonthDayNum(...), $this->byMonthDay);
        }

        if ($this->byYearDay !== null) {
            \array_map(RuleValidator::assertYearDayNum(...), $this->byYearDay);
        }

        if ($this->byWeekNo !== null) {
            \array_map(RuleValidator::assertWeekNum(...), $this->byWeekNo);
        }

        if ($this->byMonth !== null) {
            \array_map(RuleValidator::assertMonthNum(...), $this->byMonth);
        }

        if ($this->bySetPos !== null) {
            \array_map(RuleValidator::assertYearDayNum(...), $this->bySetPos);
        }

        if ($this->byDay !== null && !($this->freq === Freq::Monthly || $this->freq === Freq::Yearly)) {
            throw new InvalidArgumentException('The BYDAY rule part MUST NOT be specified with a numeric value when the FREQ rule part is not set to MONTHLY or YEARLY.');
        }

        if (
            $this->byDay !== null &&
            \array_sum(\array_map(
                static fn (WeekDayNum $weekDayNum) => $weekDayNum->ordWeek !== null,
                $this->byDay
            )) &&
            $this->byWeekNo !== null &&
            $this->freq === Freq::Yearly
        ) {
            throw new InvalidArgumentException(
                'The BYDAY rule part with week ordinal MUST NOT be specified with a numeric value with the FREQ rule part set to YEARLY when the BYWEEKNO rule part is specified.',
            );
        }

        if ($this->byMonthDay !== null && $this->freq === Freq::Weekly) {
            throw new InvalidArgumentException('The BYMONTHDAY rule part MUST NOT be specified when the FREQ rule part is set to WEEKLY.');
        }

        if ($this->byYearDay !== null && ($this->freq === Freq::Daily || $this->freq === Freq::Weekly || $this->freq === Freq::Monthly)) {
            throw new InvalidArgumentException('The BYYEARDAY rule part MUST NOT be specified when the FREQ rule part is set to DAILY, WEEKLY, or MONTHLY.');
        }

        if ($this->byWeekNo !== null && $this->freq !== Freq::Yearly) {
            throw new InvalidArgumentException('The BYWEEKNO rule part MUST NOT be used when the FREQ rule part is set to anything other than YEARLY.');
        }

        if (
            $this->bySetPos !== null &&
            $this->byWeekNo === null &&
            $this->byYearDay === null &&
            $this->byMonthDay === null &&
            $this->byDay === null &&
            $this->byMonth === null &&
            $this->byHour === null &&
            $this->byMinute === null &&
            $this->bySecond === null
        ) {
            throw new InvalidArgumentException('The BYSETPOS rule part MUST only be used in conjunction with another BYxxx rule part.');
        }
    }

    /**
     * Return true if the rrule has an end condition, false otherwise
     */
    public function isFinite(): bool
    {
        return $this->count !== null || $this->until !== null;
    }

    /**
     * Return true if the rrule has no end condition (infite)
     */
    public function isInfinite(): bool
    {
        return $this->count === null && $this->until === null;
    }

    /**
     * Format a rule according to RFC 5545 and RFC 2445
     */
    public function toString(): string
    {
        $result = 'FREQ=' . $this->freq->value;
        if ($this->until) {
            $result .= ';UNTIL=' . $this->until->format('Ymd\THis');
        }

        if ($this->count) {
            $result .= ';COUNT=' . $this->count;
        }

        if ($this->interval) {
            $result .= ';INTERVAL=' . $this->interval;
        }

        if ($this->bySecond) {
            $result .= ';BYSECOND=' . \implode(',', $this->bySecond);
        }

        if ($this->byMinute) {
            $result .= ';BYMINUTE=' . \implode(',', $this->byMinute);
        }

        if ($this->byHour) {
            $result .= ';BYHOUR=' . \implode(',', $this->byHour);
        }

        if ($this->byDay) {
            $result .= ';BYDAY=' . \implode(',', $this->byDay);
        }

        if ($this->byMonthDay) {
            $result .= ';BYMONTHDAY=' . \implode(',', $this->byMonthDay);
        }

        if ($this->byYearDay) {
            $result .= ';BYYEARDAY=' . \implode(',', $this->byYearDay);
        }

        if ($this->byWeekNo) {
            $result .= ';BYWEEKNO=' . \implode(',', $this->byWeekNo);
        }

        if ($this->byMonth) {
            $result .= ';BYMONTH=' . \implode(',', $this->byMonth);
        }

        if ($this->bySetPos) {
            $result .= ';BYSETPOS=' . \implode(',', $this->bySetPos);
        }

        if ($this->workWeekStart) {
            $result .= ';WKST=' . $this->workWeekStart->value;
        }

        return $result;
    }
}
