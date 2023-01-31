<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\Tests;

use Brzuchal\RecurrenceRule\Freq;
use Brzuchal\RecurrenceRule\Rule;
use Brzuchal\RecurrenceRule\RuleFactory;
use Brzuchal\RecurrenceRule\WeekDay;
use Brzuchal\RecurrenceRule\WeekDayNum;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class RuleFactoryTest extends TestCase
{
    /**
     * @dataProvider dataValidPartialRules
     */
    public function testValidRule(string $rule, Rule $expected): void
    {
        $this->assertEquals($expected, RuleFactory::fromString($rule));
    }

    public function dataValidPartialRules(): array
    {
        return [
            // single
            'Secondly freq' => [
                'FREQ=SECONDLY',
                new Rule(freq: Freq::Secondly),
            ],
            'Minutely freq' => [
                'FREQ=MINUTELY',
                new Rule(freq: Freq::Minutely),
            ],
            'Hourly freq' => [
                'FREQ=HOURLY',
                new Rule(freq: Freq::Hourly),
            ],
            'Daily freq' => [
                'FREQ=DAILY',
                new Rule(freq: Freq::Daily),
            ],
            'Weekly freq' => [
                'FREQ=WEEKLY',
                new Rule(freq: Freq::Weekly),
            ],
            'Monthly freq' => [
                'FREQ=MONTHLY',
                new Rule(freq: Freq::Monthly),
            ],
            'Yearly freq' => [
                'FREQ=YEARLY',
                new Rule(freq: Freq::Yearly),
            ],
            'Until 1999-12-31' => [
                'FREQ=YEARLY;UNTIL=19991231',
                new Rule(freq:Freq::Yearly, until: new DateTimeImmutable('1999-12-31 23:59:59')),
            ],
            'Until 1999-12-31 and 12:00:00' => [
                'FREQ=YEARLY;UNTIL=19991231T120000',
                new Rule(freq: Freq::Yearly, until: new DateTimeImmutable('1999-12-31 12:00:00')),
            ],
            'Count 1' => [
                'FREQ=YEARLY;COUNT=1',
                new Rule(freq: Freq::Yearly, count: 1),
            ],
            'Count 99' => [
                'FREQ=YEARLY;COUNT=99',
                new Rule(freq:Freq::Yearly, count: 99),
            ],
            'Interval 33' => [
                'FREQ=YEARLY;INTERVAL=33',
                new Rule(freq: Freq::Yearly, interval: 33),
            ],
            'BySeconds 1' => [
                'FREQ=YEARLY;BYSECOND=1',
                new Rule(freq: Freq::Yearly, bySecond: [1]),
            ],
            'BySeconds 1, 31' => [
                'FREQ=YEARLY;BYSECOND=1,31',
                new Rule(freq: Freq::Yearly, bySecond: [1, 31]),
            ],
            'ByMinute 1, 31' => [
                'FREQ=YEARLY;BYMINUTE=1,31',
                new Rule(freq: Freq::Yearly, byMinute: [1, 31]),
            ],
            'ByHour 1, 23' => [
                'FREQ=YEARLY;BYHOUR=1,23',
                new Rule(freq: Freq::Yearly, byHour: [1, 23]),
            ],
            'ByDay MO,TU' => [
                'FREQ=YEARLY;BYDAY=MO,TU',
                new Rule(freq: Freq::Yearly, byDay: [
                    new WeekDayNum(WeekDay::Monday),
                    new WeekDayNum(WeekDay::Tuesday),
                ]),
            ],
            'Weekly ByDay MO,TU' => [
                'FREQ=WEEKLY;BYDAY=MO,TU',
                new Rule(freq: Freq::Weekly, byDay: [
                    new WeekDayNum(WeekDay::Monday),
                    new WeekDayNum(WeekDay::Tuesday),
                ]),
            ],
            'ByDay 2MO,+3TU,-4WE' => [
                'FREQ=YEARLY;BYDAY=2MO,+3TU,-4WE',
                new Rule(
                    freq: Freq::Yearly,
                    byDay: [
                        WeekDay::Monday->num(2),
                        WeekDay::Tuesday->num(3),
                        WeekDay::Wednesday->num(-4),
                    ],
                ),
            ],
            'ByMonthDay 1,2' => [
                'FREQ=YEARLY;BYMONTHDAY=1,2',
                new Rule(freq: Freq::Yearly, byMonthDay: [1, 2]),
            ],
            'ByMonthDay +1,-2' => [
                'FREQ=YEARLY;BYMONTHDAY=+1,-2',
                new Rule(freq: Freq::Yearly, byMonthDay: [1, -2]),
            ],
            'ByYearDay 128,256' => [
                'FREQ=YEARLY;BYYEARDAY=128,256',
                new Rule(freq: Freq::Yearly, byYearDay: [128, 256]),
            ],
            'ByYearDay +16,-32' => [
                'FREQ=YEARLY;BYYEARDAY=+16,-32',
                new Rule(freq: Freq::Yearly, byYearDay: [16, -32]),
            ],
            'ByWeekNo 1,2' => [
                'FREQ=YEARLY;BYWEEKNO=1,2',
                new Rule(freq: Freq::Yearly, byWeekNo: [1, 2]),
            ],
            'ByWeekNo +16,-32' => [
                'FREQ=YEARLY;BYWEEKNO=+16,-32',
                new Rule(freq: Freq::Yearly, byWeekNo: [16, -32]),
            ],
            'ByMonth 1,2' => [
                'FREQ=YEARLY;BYMONTH=1,2',
                new Rule(freq: Freq::Yearly, byMonth: [1, 2]),
            ],
            'BySetPos 128' => [
                'FREQ=YEARLY;BYSETPOS=128,256;BYMONTH=1',
                new Rule(freq: Freq::Yearly, byMonth: [1], bySetPos: [128, 256]),
            ],
            'Wkst MO' => [
                'FREQ=YEARLY;WKST=MO',
                new Rule(freq: Freq::Yearly, workWeekStart: WeekDay::Monday),
            ],
            // multiple
            'Daily Until 2020-01-01 Count 2' => [
                'FREQ=DAILY;UNTIL=20200101;COUNT=2',
                new Rule(freq: Freq::Daily, until: new DateTimeImmutable('2020-01-01 23:59:59'), count: 2),
            ],
            'Daily ByHour 0, 6, 12, 18 Count 12' => [
                'FREQ=DAILY;BYHOUR=0,6,12,18;COUNT=12',
                new Rule(freq: Freq::Daily, count: 12, byHour: [0, 6, 12, 18]),
            ],
        ];
    }
}
