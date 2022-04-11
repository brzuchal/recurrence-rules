<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\Tests;

use Brzuchal\RecurrenceRule\Freq;
use Brzuchal\RecurrenceRule\Rule;
use Brzuchal\RecurrenceRule\RuleBuilder;
use Brzuchal\RecurrenceRule\WeekDay;
use Brzuchal\RecurrenceRule\WeekDayNum;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class RuleBuilderTest extends TestCase
{
    public function testSecondlyBuild(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Secondly),
            RuleBuilder::secondly()->build(),
        );
    }

    public function testMinutelyBuild(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Minutely),
            RuleBuilder::minutely()->build(),
        );
    }

    public function testHourlyBuild(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Hourly),
            RuleBuilder::hourly()->build(),
        );
    }

    public function testDailyBuild(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Daily),
            RuleBuilder::daily()->build(),
        );
    }

    public function testWeeklyBuild(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Weekly),
            RuleBuilder::weekly()->build(),
        );
    }

    public function testMonthlyBuild(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Monthly),
            RuleBuilder::monthly()->build(),
        );
    }

    public function testYearlyBuild(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Yearly),
            RuleBuilder::yearly()->build(),
        );
    }

    public function testDailyUntil(): void
    {
        $dateTime = new DateTimeImmutable('2021-12-31 23:59:59');
        $this->assertEquals(
            new Rule(freq: Freq::Daily, until: $dateTime),
            RuleBuilder::daily()->until($dateTime)->build(),
        );
    }

    public function testDailyCount(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Daily, count: 2),
            RuleBuilder::daily()->count(2)->build(),
        );
    }

    public function testDailyInterval(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Daily, interval: 2),
            RuleBuilder::daily()->interval(2)->build(),
        );
    }

    public function testMinutelyBySecond(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Minutely, bySecond: [1, 31]),
            RuleBuilder::minutely()->bySecond(1, 31)->build(),
        );
    }

    public function testHourlyByMinute(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Hourly, byMinute: [1, 31]),
            RuleBuilder::hourly()->byMinute(1, 31)->build(),
        );
    }

    public function testDailyByHour(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Daily, byHour: [1, 23]),
            RuleBuilder::daily()->byHour(1, 23)->build(),
        );
    }

    public function testMonthlyByWeekDay(): void
    {
        $weekDayNum = new WeekDayNum(WeekDay::Monday, 1);
        $this->assertEquals(
            new Rule(freq: Freq::Monthly, byDay: [$weekDayNum]),
            RuleBuilder::monthly()->byDay($weekDayNum)->build(),
        );
    }

    public function testYearlyByMonthDay(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Yearly, byMonthDay: [1]),
            RuleBuilder::yearly()->byMonthDay(1)->build(),
        );
    }

    public function testYearlyByYearDay(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Yearly, byYearDay: [128]),
            RuleBuilder::yearly()->byYearDay(128)->build(),
        );
    }

    public function testYearlyByWeekNo(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Yearly, byWeekNo: [16]),
            RuleBuilder::yearly()->byWeekNo(16)->build(),
        );
    }

    public function testYearlyByMonth(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Yearly, byMonth: [2]),
            RuleBuilder::yearly()->byMonth(2)->build(),
        );
    }

    public function testYearlyBySetPos(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Yearly, byMonth: [2], bySetPos: [128]),
            RuleBuilder::yearly()->byMonth(2)->bySetPos(128)->build(),
        );
    }

    public function testYearlyWkst(): void
    {
        $this->assertEquals(
            new Rule(freq: Freq::Yearly, workWeekStart: WeekDay::Monday),
            RuleBuilder::yearly()->workWeekStart(WeekDay::Monday)->build(),
        );
    }
}
