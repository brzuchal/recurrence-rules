<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

use Brzuchal\RecurrenceRule\ValueObject\MonthDayNum;
use Brzuchal\RecurrenceRule\ValueObject\MonthNum;
use Brzuchal\RecurrenceRule\ValueObject\WeekDayNum;
use Brzuchal\RecurrenceRule\ValueObject\WeekNum;
use Brzuchal\RecurrenceRule\ValueObject\YearDayNum;
use DateTimeImmutable;

// Do not be silent! #StopWar 🇺🇦 #StandWithUkraine #StopPutin
final class Rule
{
    /**
     * @psalm-param positive-int|null $count
     * @psalm-param positive-int|null $interval
     * @psalm-param list<positive-int>|null $bySecond
     * @psalm-param list<positive-int>|null $byMinute
     * @psalm-param list<positive-int>|null $byHour
     * @psalm-param list<WeekDayNum>|null $byDay
     * @psalm-param list<MonthDayNum>|null $byMonthDay
     * @psalm-param list<YearDayNum>|null $byYearDay
     * @psalm-param list<WeekNum>|null $byWeekNo
     * @psalm-param list<MonthNum>|null $byMonth
     * @psalm-param list<YearDayNum>|null $bySetPos
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
        if ($this->byDay !== null && !($this->freq === Freq::Monthly || $this->freq === Freq::Yearly)) {
            throw new \InvalidArgumentException('The BYDAY rule part MUST NOT be specified with a numeric value when the FREQ rule part is not set to MONTHLY or YEARLY.');
        }

        if ($this->byDay !== null && $this->byWeekNo !== null && $this->freq === Freq::Yearly) {
            throw new \InvalidArgumentException('The BYDAY rule part MUST NOT be specified with a numeric value with the FREQ rule part set to YEARLY when the BYWEEKNO rule part is specified.');
        }

        if ($this->byMonthDay !== null && $this->freq === Freq::Weekly) {
            throw new \InvalidArgumentException('The BYMONTHDAY rule part MUST NOT be specified when the FREQ rule part is set to WEEKLY.');
        }

        if ($this->byYearDay !== null && ($this->freq === Freq::Daily || $this->freq === Freq::Weekly || $this->freq === Freq::Monthly)) {
            throw new \InvalidArgumentException('The BYYEARDAY rule part MUST NOT be specified when the FREQ rule part is set to DAILY, WEEKLY, or MONTHLY.');
        }

        if ($this->byWeekNo !== null && $this->freq !== Freq::Yearly) {
            throw new \InvalidArgumentException('The BYWEEKNO rule part MUST NOT be used when the FREQ rule part is set to anything other than YEARLY.');
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
            throw new \InvalidArgumentException('The BYSETPOS rule part MUST only be used in conjunction with another BYxxx rule part.');
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
