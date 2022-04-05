<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\Tests;

use Brzuchal\RecurrenceRule\Freq;
use Brzuchal\RecurrenceRule\Rule;
use Brzuchal\RecurrenceRule\RuleBuilder;
use Brzuchal\RecurrenceRule\ValueObject\MonthDayNum;
use Brzuchal\RecurrenceRule\ValueObject\MonthNum;
use Brzuchal\RecurrenceRule\ValueObject\WeekDayNum;
use Brzuchal\RecurrenceRule\ValueObject\WeekNum;
use Brzuchal\RecurrenceRule\ValueObject\YearDayNum;
use Brzuchal\RecurrenceRule\WeekDay;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class RuleBuilderTest extends TestCase
{
    public function testSecondlyBuild(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Secondly),
            (new RuleBuilder())->secondly()->build(),
        );
    }

    public function testMinutelyBuild(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Minutely),
            (new RuleBuilder())->minutely()->build(),
        );
    }

    public function testHourlyBuild(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Hourly),
            (new RuleBuilder())->hourly()->build(),
        );
    }

    public function testDailyBuild(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Daily),
            (new RuleBuilder())->daily()->build(),
        );
    }

    public function testWeeklyBuild(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Weekly),
            (new RuleBuilder())->weekly()->build(),
        );
    }

    public function testMonthlyBuild(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Monthly),
            (new RuleBuilder())->monthly()->build(),
        );
    }

    public function testYearlyBuild(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Yearly),
            (new RuleBuilder())->yearly()->build(),
        );
    }

    public function testDailyUntil(): void
    {
        $dateTime = new DateTimeImmutable('2021-12-31 23:59:59');
        $this->assertEquals(
            new Rule(freq: Freq::Daily, until: $dateTime),
            (new RuleBuilder())->daily()->until($dateTime)->build(),
        );
    }

    public function testDailyCount(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Daily, count: 2),
            (new RuleBuilder())->daily()->count(2)->build(),
        );
    }

    public function testDailyInterval(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Daily, interval: 2),
            (new RuleBuilder())->daily()->interval(2)->build(),
        );
    }

    public function testMinutelyBySecond(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Minutely, bySecond: [1, 31]),
            (new RuleBuilder())->minutely()->bySecond([1, 31])->build(),
        );
    }

    public function testHourlyByMinute(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Hourly, byMinute: [1, 31]),
            (new RuleBuilder())->hourly()->byMinute([1, 31])->build(),
        );
    }

    public function testDailyByHour(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Daily, byHour: [1, 23]),
            (new RuleBuilder())->daily()->byHour([1, 23])->build(),
        );
    }

    public function testMonthlyByWeekDay(): void
    {
        $weekDayNum = new WeekDayNum(WeekDay::Monday, 1);
        $this->assertEquals(
            new Rule(freq: Freq::Monthly, byDay: [$weekDayNum]),
            (new RuleBuilder())->monthly()->byDay([$weekDayNum])->build(),
        );
    }

    public function testYearlyByMonthDay(): void
    {
        $monthDay = new MonthDayNum(1);
        $this->assertEquals(
            new Rule(freq: Freq::Yearly, byMonthDay: [$monthDay]),
            (new RuleBuilder())->yearly()->byMonthDay([$monthDay])->build(),
        );
    }

    public function testYearlyByYearDay(): void
    {
        $yearDay = new YearDayNum(128);
        $this->assertEquals(
            new Rule(freq: Freq::Yearly, byYearDay: [$yearDay]),
            (new RuleBuilder())->yearly()->byYearDay([$yearDay])->build(),
        );
    }

    public function testYearlyByWeekNo(): void
    {
        $weekNum = new WeekNum(16);
        $this->assertEquals(
            new Rule(freq: Freq::Yearly, byWeekNo: [$weekNum]),
            (new RuleBuilder())->yearly()->byWeekNo([$weekNum])->build(),
        );
    }

    public function testYearlyByMonth(): void
    {
        $monthNum = new MonthNum(2);
        $this->assertEquals(
            new Rule(freq: Freq::Yearly, byMonth: [$monthNum]),
            (new RuleBuilder())->yearly()->byMonth([$monthNum])->build(),
        );
    }

    public function testYearlyBySetPos(): void
    {
        $monthNum = new MonthNum(2);
        $yearDay = new YearDayNum(128);
        $this->assertEquals(
            new Rule(freq: Freq::Yearly, byMonth: [$monthNum], bySetPos: [$yearDay]),
            (new RuleBuilder())->yearly()->byMonth([$monthNum])->bySetPos([$yearDay])->build(),
        );
    }

    public function testYearlyWkst(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Yearly, workWeekStart: WeekDay::Monday),
            (new RuleBuilder())->yearly()->workWeekStart(WeekDay::Monday)->build(),
        );
    }
}
