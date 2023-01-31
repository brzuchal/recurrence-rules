<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

use DateTimeImmutable;

/**
 * @psalm-import-type second from Rule
 * @psalm-import-type minute from Rule
 * @psalm-import-type hour from Rule
 * @psalm-import-type monthday from Rule
 * @psalm-import-type yearday from Rule
 * @psalm-import-type weekno from Rule
 * @psalm-import-type monthno from Rule
 */
final class RuleBuilder
{
    private DateTimeImmutable|null $until = null;
    /** @psalm-var positive-int|null */
    private int|null $interval = null;
    /** @psalm-var positive-int|null */
    private int|null $count = null;
    /** @psalm-var non-empty-list<second>|null */
    private array|null $secList = null;
    /** @psalm-var non-empty-list<minute>|null */
    private array|null $minList = null;
    /** @psalm-var non-empty-list<hour>|null */
    private array|null $hrList = null;
    /** @psalm-var non-empty-list<WeekDayNum>|null */
    private array|null $dList = null;
    /** @psalm-var non-empty-list<monthday>|null */
    private array|null $mdList = null;
    /** @psalm-var non-empty-list<yearday>|null */
    private array|null $ydList = null;
    /** @psalm-var non-empty-list<weekno>|null */
    private array|null $wnList = null;
    /** @psalm-var non-empty-list<monthno>|null  */
    private array|null $moList = null;
    /** @psalm-var non-empty-list<yearday>|null */
    private array|null $spList = null;
    private WeekDay|null $wkst = null;

    public function __construct(
        private Freq $freq
    ) {
    }

    public static function secondly(): self
    {
        return new self(Freq::Secondly);
    }

    public static function minutely(): self
    {
        return new self(Freq::Minutely);
    }

    public static function hourly(): self
    {
        return new self(Freq::Hourly);
    }

    public static function daily(): self
    {
        return new self(Freq::Daily);
    }

    public static function weekly(): self
    {
        return new self(Freq::Weekly);
    }

    public static function monthly(): self
    {
        return new self(Freq::Monthly);
    }

    public static function yearly(): self
    {
        return new self(Freq::Yearly);
    }

    public function until(DateTimeImmutable $dateTime): self
    {
        $this->until = $dateTime;

        return $this;
    }

    /**
     * @param positive-int $count
     *
     * @return $this
     */
    public function count(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @psalm-param positive-int $interval
     *
     * @return $this
     */
    public function interval(int $interval): self
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * @param second ...$secList
     *
     * @return $this
     */
    public function bySecond(int ...$secList): self
    {
        /** @psalm-var non-empty-list<second> secList */
        $this->secList = $secList;

        return $this;
    }

    /**
     * @param minute ...$minList
     *
     * @return $this
     */
    public function byMinute(int ...$minList): self
    {
        /** @psalm-var non-empty-list<minute> minList */
        $this->minList = $minList;

        return $this;
    }

    /**
     * @param hour ...$hrList
     *
     * @return $this
     */
    public function byHour(int ...$hrList): self
    {
        /** @psalm-var non-empty-list<hour> hrList */
        $this->hrList = $hrList;

        return $this;
    }

    /**
     * @return $this
     */
    public function byDay(WeekDayNum ...$dList): self
    {
        /** @psalm-var non-empty-list<WeekDayNum> dList */
        $this->dList = $dList;

        return $this;
    }

    /**
     * @param monthday ...$mdList
     *
     * @return $this
     */
    public function byMonthDay(int ...$mdList): self
    {
        /** @psalm-var non-empty-list<monthday> mdList */
        $this->mdList = $mdList;

        return $this;
    }

    /**
     * @param yearday ...$ydList
     *
     * @return $this
     */
    public function byYearDay(int ...$ydList): self
    {
        /** @psalm-var non-empty-list<yearday> ydList */
        $this->ydList = $ydList;

        return $this;
    }

    /**
     * @param weekno ...$wnList
     *
     * @return $this
     */
    public function byWeekNo(int ...$wnList): self
    {
        /** @psalm-var non-empty-list<weekno> wnList */
        $this->wnList = $wnList;

        return $this;
    }

    /**
     * @param monthno ...$moList
     *
     * @return $this
     */
    public function byMonth(int ...$moList): self
    {
        /** @psalm-var non-empty-list<monthno> moList */
        $this->moList = $moList;

        return $this;
    }

    /**
     * @param yearday ...$spList
     *
     * @return $this
     */
    public function bySetPos(int ...$spList): self
    {
        /** @psalm-var non-empty-list<yearday> spList */
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
