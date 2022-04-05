<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\Tests;

use Brzuchal\RecurrenceRule\Freq;
use Brzuchal\RecurrenceRule\PartialRule\ByDayRule;
use Brzuchal\RecurrenceRule\PartialRule\ByHourRule;
use Brzuchal\RecurrenceRule\PartialRule\ByMinuteRule;
use Brzuchal\RecurrenceRule\PartialRule\ByMonthDayRule;
use Brzuchal\RecurrenceRule\PartialRule\ByMonthRule;
use Brzuchal\RecurrenceRule\PartialRule\BySecondRule;
use Brzuchal\RecurrenceRule\PartialRule\BySetPosRule;
use Brzuchal\RecurrenceRule\PartialRule\ByWeekNoRule;
use Brzuchal\RecurrenceRule\PartialRule\ByYearDayRule;
use Brzuchal\RecurrenceRule\PartialRule\CountRule;
use Brzuchal\RecurrenceRule\PartialRule\FreqRule;
use Brzuchal\RecurrenceRule\PartialRule\IntervalRule;
use Brzuchal\RecurrenceRule\PartialRule\UntilRule;
use Brzuchal\RecurrenceRule\PartialRule\WkstRule;
use Brzuchal\RecurrenceRule\RfcParser;
use Brzuchal\RecurrenceRule\ValueObject\MonthDayNum;
use Brzuchal\RecurrenceRule\ValueObject\MonthNum;
use Brzuchal\RecurrenceRule\ValueObject\WeekDayNum;
use Brzuchal\RecurrenceRule\ValueObject\WeekNum;
use Brzuchal\RecurrenceRule\ValueObject\YearDayNum;
use Brzuchal\RecurrenceRule\WeekDay;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class RfcParserTest extends TestCase
{
    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification
     * @dataProvider dataValidPartialRules
     */
    public function testValidRule(string $rule, array $expected): void
    {
        $this->assertEquals($expected, RfcParser::fromString($rule));
    }

    public function dataValidPartialRules(): array
    {
        return [
            // single
            'Secondly freq' => ['FREQ=SECONDLY', [new FreqRule(Freq::Secondly)]],
            'Minutely freq' => ['FREQ=MINUTELY', [new FreqRule(Freq::Minutely)]],
            'Hourly freq' => ['FREQ=HOURLY', [new FreqRule(Freq::Hourly)]],
            'Daily freq' => ['FREQ=DAILY', [new FreqRule(Freq::Daily)]],
            'Weekly freq' => ['FREQ=WEEKLY', [new FreqRule(Freq::Weekly)]],
            'Monthly freq' => ['FREQ=MONTHLY', [new FreqRule(Freq::Monthly)]],
            'Yearly freq' => ['FREQ=YEARLY', [new FreqRule(Freq::Yearly)]],
            'Until 1999-12-31' => ['UNTIL=19991231', [new UntilRule(new DateTimeImmutable('1999-12-31 23:59:59'))]],
            'Until 1999-12-31 and 12:00:00' => ['UNTIL=19991231T120000', [new UntilRule(new DateTimeImmutable('1999-12-31 12:00:00'))]],
            'Count 1' => ['COUNT=1', [new CountRule(1)]],
            'Count 99' => ['COUNT=99', [new CountRule(99)]],
            'Interval 33' => ['INTERVAL=33', [new IntervalRule(33)]],
            'BySeconds 1' => ['BYSECOND=1', [new BySecondRule([1])]],
            'BySeconds 1, 31' => ['BYSECOND=1,31', [new BySecondRule([1, 31])]],
            'ByMinute 1, 31' => ['BYMINUTE=1,31', [new ByMinuteRule([1, 31])]],
            'ByHour 1, 23' => ['BYHOUR=1,23', [new ByHourRule([1, 23])]],
            'ByDay MO,TU' => [
                'BYDAY=MO,TU',
                [
                    new ByDayRule([
                        new WeekDayNum(WeekDay::Monday),
                        new WeekDayNum(WeekDay::Tuesday),
                    ]),
                ],
            ],
            'ByDay 2MO,+3TU,-4WE' => [
                'BYDAY=2MO,+3TU,-4WE',
                [
                    new ByDayRule([
                        new WeekDayNum(WeekDay::Monday, 2),
                        new WeekDayNum(WeekDay::Tuesday, 3, false),
                        new WeekDayNum(WeekDay::Wednesday, 4, true),
                    ]),
                ],
            ],
            'ByMonthDay 1,2' => [
                'BYMONTHDAY=1,2',
                [
                    new ByMonthDayRule([
                        new MonthDayNum(1),
                        new MonthDayNum(2),
                    ]),
                ],
            ],
            'ByMonthDay +1,-2' => [
                'BYMONTHDAY=+1,-2',
                [
                    new ByMonthDayRule([
                        new MonthDayNum(1, false),
                        new MonthDayNum(2, true),
                    ]),
                ],
            ],
            'ByYearDay 128,256' => [
                'BYYEARDAY=128,256',
                [
                    new ByYearDayRule([
                        new YearDayNum(128),
                        new YearDayNum(256),
                    ]),
                ],
            ],
            'ByYearDay +16,-32' => [
                'BYYEARDAY=+16,-32',
                [
                    new ByYearDayRule([
                        new YearDayNum(16, false),
                        new YearDayNum(32, true),
                    ]),
                ],
            ],
            'ByWeekNo 1,2' => ['BYWEEKNO=1,2', [new ByWeekNoRule([new WeekNum(1), new WeekNum(2)])]],
            'ByWeekNo +16,-32' => ['BYWEEKNO=+16,-32', [new ByWeekNoRule([new WeekNum(16, false), new WeekNum(32, true)])]],
            'ByMonth 1,2' => ['BYMONTH=1,2', [new ByMonthRule([new MonthNum(1), new MonthNum(2)])]],
            'BySetPos 128' => ['BYSETPOS=128,256', [new BySetPosRule([new YearDayNum(128), new YearDayNum(256)])]],
            'Wkst MO' => ['WKST=MO', [new WkstRule(WeekDay::Monday)]],
            // multiple
            'Daily Until 2020-01-01 Count 2' => [
                'FREQ=DAILY;UNTIL=20200101;COUNT=2',
                [new FreqRule(Freq::Daily), new UntilRule(new DateTimeImmutable('2020-01-01 23:59:59')), new CountRule(2)],
            ],
            'Daily ByHour 0, 6, 12, 18 Count 12' => [
                'FREQ=DAILY;BYHOUR=0,6,12,18;COUNT=12',
                [new FreqRule(Freq::Daily), new ByHourRule([0, 6, 12, 18]), new CountRule(12)],
            ],
        ];
    }
}
