<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

use Brzuchal\RecurrenceRule\ValueObject\MonthDayNum;
use Brzuchal\RecurrenceRule\ValueObject\MonthNum;
use Brzuchal\RecurrenceRule\ValueObject\WeekDayNum;
use Brzuchal\RecurrenceRule\ValueObject\WeekNum;
use Brzuchal\RecurrenceRule\ValueObject\YearDayNum;
use DateTimeImmutable;

final class RuleBuilder
{
    private Freq|null $freq = null;
    private DateTimeImmutable|null $until = null;
    /** @psalm-var positive-int|null */
    private int|null $interval = null;
    /** @psalm-var positive-int|null */
    private int|null $count = null;
    private array|null $secList = null;
    private array|null $minList = null;
    private array|null $hrList = null;
    /** @psalm-var list<WeekDayNum>|null */
    private array|null $dList = null;
    /** @psalm-var list<MonthDayNum>|null */
    private array|null $mdList = null;
    /** @psalm-var list<YearDayNum>|null */
    private array|null $ydList = null;
    /** @psalm-var list<WeekNum>|null */
    private array|null $wnList = null;
    /** @psalm-var list<MonthNum>|null  */
    private array|null $moList = null;
    /** @psalm-var list<YearDayNum>|null */
    private array|null $spList = null;
    private WeekDay|null $wkst = null;

    public function secondly(): self
    {
        $this->freq = Freq::Secondly;

        return $this;
    }

    public function minutely(): self
    {
        $this->freq = Freq::Minutely;

        return $this;
    }

    public function hourly(): self
    {
        $this->freq = Freq::Hourly;

        return $this;
    }

    public function daily(): self
    {
        $this->freq = Freq::Daily;

        return $this;
    }

    public function weekly(): self
    {
        $this->freq = Freq::Weekly;

        return $this;
    }

    public function monthly(): self
    {
        $this->freq = Freq::Monthly;

        return $this;
    }

    public function yearly(): self
    {
        $this->freq = Freq::Yearly;

        return $this;
    }

    public function freq(Freq $freq): self
    {
        $this->freq = $freq;

        return $this;
    }

    public function until(DateTimeImmutable $dateTime): self
    {
        $this->until = $dateTime;

        return $this;
    }

    public function count(int $count): self
    {
        if ($count < 0) {
            throw new \InvalidArgumentException('Expected positive int');
        }

        \assert($count > 1);
        $this->count = $count;

        return $this;
    }

    /**
     * @psalm-param positive-int $interval
     */
    public function interval(int $interval): self
    {
        $this->interval = $interval;

        return $this;
    }

    public function bySecond(array $secList): self
    {
        if (!empty(\array_filter($secList, static fn ($sec) => !\is_int($sec) || $sec > 60 || $sec < 0))) {
            throw new \UnexpectedValueException('Expected array of seconds');
        }

        $this->secList = $secList;

        return $this;
    }

    public function byMinute(array $minList): self
    {
        if (!empty(\array_filter($minList, static fn ($min) => !\is_int($min) || $min > 59 || $min < 0))) {
            throw new \UnexpectedValueException('Expected array of minutes');
        }

        $this->minList = $minList;

        return $this;
    }

    public function byHour(array $hrList): self
    {
        if (!empty(\array_filter($hrList, static fn ($hour) => !\is_int($hour) || $hour > 23 || $hour < 0))) {
            throw new \UnexpectedValueException('Expected array of hours');
        }

        $this->hrList = $hrList;

        return $this;
    }

    /**
     * @psalm-param list<WeekDayNum> $dList
     * @return $this
     */
    public function byDay(array $dList): self
    {
        if (!empty(\array_filter($dList, static fn ($day) => !($day instanceof WeekDayNum)))) {
            throw new \UnexpectedValueException('Expected array of ' . WeekDayNum::class);
        }

        $this->dList = $dList;

        return $this;
    }

    public function byMonthDay(array $mdList): self
    {
        if (!empty(\array_filter($mdList, static fn ($monthDay) => !($monthDay instanceof MonthDayNum)))) {
            throw new \UnexpectedValueException('Expected array of ' . MonthDayNum::class);
        }

        $this->mdList = $mdList;

        return $this;
    }

    public function byYearDay(array $ydList): self
    {
        if (!empty(\array_filter($ydList, static fn ($yearDay) => !($yearDay instanceof YearDayNum)))) {
            throw new \UnexpectedValueException('Expected array of ' . YearDayNum::class);
        }

        $this->ydList = $ydList;

        return $this;
    }

    public function byWeekNo(array $wnList): self
    {
        if (!empty(\array_filter($wnList, static fn ($weekNum) => !($weekNum instanceof WeekNum)))) {
            throw new \UnexpectedValueException('Expected array of ' . WeekNum::class);
        }

        $this->wnList = $wnList;

        return $this;
    }

    public function byMonth(array $moList): self
    {
        if (!empty(\array_filter($moList, static fn ($monthNum) => !($monthNum instanceof MonthNum)))) {
            throw new \UnexpectedValueException('Expected array of ' . MonthNum::class);
        }

        $this->moList = $moList;

        return $this;
    }

    public function bySetPos(array $spList): self
    {
        if (!empty(\array_filter($spList, static fn ($yearDay) => !($yearDay instanceof YearDayNum)))) {
            throw new \UnexpectedValueException('Expected array of ' . YearDayNum::class);
        }

        $this->spList = $spList;

        return $this;
    }

    public function workWeekStart(WeekDay $day): self
    {
        $this->wkst = $day;

        return $this;
    }

    public function build(): Rule
    {
        if ($this->freq === null) {
            throw new \BadMethodCallException('Frequency has to be defined first');
        }

        return new Rule(
            freq: $this->freq,
            until: $this->until,
            count: $this->count,
            interval: $this->interval,
            bySecond: $this->secList,
            byMinute: $this->minList,
            byHour: $this->hrList,
            byDay: $this->dList,
            byMonthDay: $this->mdList,
            byYearDay: $this->ydList,
            byWeekNo: $this->wnList,
            byMonth: $this->moList,
            bySetPos: $this->spList,
            workWeekStart: $this->wkst,
        );
    }
}
