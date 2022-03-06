<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\Tests;

use Brzuchal\RecurrenceRule\Freq;
use Brzuchal\RecurrenceRule\Rule;
use Brzuchal\RecurrenceRule\ValueObject\YearDayNum;
use Brzuchal\RecurrenceRule\WeekDay;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

// Do not be silent! #StopWar 🇺🇦 #StandWithUkraine #StopPutin
class RuleTest extends TestCase
{
    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification
     * @dataProvider dataTestToString
     */
    public function testToString(array $expected, Rule $rule): void
    {
        \sort($expected);
        $test = \explode(';', $rule->toString());
        \sort($test);
        $this->assertEquals($expected, $test);
    }

    public function dataTestToString(): array
    {
        return [
            'Secondly' => [['FREQ=SECONDLY'], new Rule(freq: Freq::Secondly)],
            'Minutely' => [['FREQ=MINUTELY'], new Rule(freq: Freq::Minutely)],
            'Hourly' => [['FREQ=HOURLY'], new Rule(freq: Freq::Hourly)],
            'Daily' => [['FREQ=DAILY'], new Rule(freq: Freq::Daily)],
            'Weekly' => [['FREQ=WEEKLY'], new Rule(freq: Freq::Weekly)],
            'Monthly' => [['FREQ=MONTHLY'], new Rule(freq: Freq::Monthly)],
            'Yearly' => [['FREQ=YEARLY'], new Rule(freq: Freq::Yearly)],
            'Daily Until 2021-12-31' => [
                ['UNTIL=20211231T235959', 'FREQ=DAILY'],
                new Rule(freq: Freq::Daily, until: new DateTimeImmutable('2021-12-31 23:59:59')),
            ],
            'Daily Until 2021-12-31 12:00:00' => [
                ['UNTIL=20211231T120000', 'FREQ=DAILY'],
                new Rule(freq: Freq::Daily, until: new DateTimeImmutable('2021-12-31 12:00:00')),
            ],
            'Daily Until 2021-12-31 12:00:00 Count 2' => [
                ['UNTIL=20211231T120000', 'FREQ=DAILY', 'COUNT=2'],
                new Rule(freq: Freq::Daily, until: new DateTimeImmutable('2021-12-31 12:00:00'), count: 2),
            ],
            'Daily Until 2021-12-31 12:00:00 Count 2 Interval 3' => [
                ['UNTIL=20211231T120000', 'FREQ=DAILY', 'COUNT=2', 'INTERVAL=3'],
                new Rule(freq: Freq::Daily, until: new DateTimeImmutable('2021-12-31 12:00:00'), count: 2, interval: 3),
            ],
            'Very long and complex' => [
                [
                    'FREQ=HOURLY',
                    'COUNT=1',
                    'INTERVAL=2',
                    'BYSECOND=1,2,3,4',
                    'BYMINUTE=5,10,15,20,25,30,35',
                    'BYHOUR=6,12,18,0',
                    'BYSETPOS=1,2,4,8',
                    'WKST=TU',
                    'UNTIL=20241231T235959',
                ],
                new Rule(
                    freq: Freq::Hourly,
                    until: new DateTimeImmutable('2024-12-31 23:59:59'),
                    count: 1,
                    interval: 2,
                    bySecond: [1, 2, 3, 4],
                    byMinute: [5, 10, 15, 20, 25, 30, 35],
                    byHour: [6, 12, 18, 0],
                    bySetPos: [new YearDayNum(1), new YearDayNum(2), new YearDayNum(4), new YearDayNum(8)],
                    workWeekStart: WeekDay::Tuesday,
                ),
            ],
        ];
    }
}
