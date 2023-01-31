<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule\Tests;

use Brzuchal\RecurrenceRule\Freq;
use Brzuchal\RecurrenceRule\Rule;
use Brzuchal\RecurrenceRule\RuleIterator;
use Brzuchal\RecurrenceRule\WeekDay;
use Brzuchal\RecurrenceRule\WeekDayNum;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

use function date_create;

class RuleIteratorTest extends TestCase
{
    public function testDSTOccurrences(): void
    {
        $iterator = new RuleIterator(
            new DateTimeImmutable('1997-09-02', new DateTimeZone('Europe/Warsaw')),
            new Rule(freq: Freq::Monthly, count: 8, byHour: [12]),
        );
        $occurrences = [
            date_create('1997-09-02 12:00:00', new DateTimeZone('Europe/Warsaw')),
            date_create('1997-10-02 12:00:00', new DateTimeZone('Europe/Warsaw')),
            date_create('1997-11-02 12:00:00', new DateTimeZone('Europe/Warsaw')),
            date_create('1997-12-02 12:00:00', new DateTimeZone('Europe/Warsaw')),
            date_create('1998-01-02 12:00:00', new DateTimeZone('Europe/Warsaw')),
            date_create('1998-02-02 12:00:00', new DateTimeZone('Europe/Warsaw')),
            date_create('1998-03-02 12:00:00', new DateTimeZone('Europe/Warsaw')),
            date_create('1998-04-02 12:00:00', new DateTimeZone('Europe/Warsaw')),
        ];
        foreach ($iterator as $occurence => $date) {
            $this->assertEquals($occurrences[$occurence], $date);
        }
    }

    /**
     * @psalm-param non-empty-list<DateTime> $occurrences
     *
     * @dataProvider yearlyRules
     * @dataProvider monthlyRules
     */
    public function testOccurrences(Rule $rule, array $occurrences): void
    {
        $iterator = new RuleIterator(new DateTimeImmutable('1997-09-02'), $rule);
        foreach ($iterator as $occurence => $date) {
            \assert($occurrences[$occurence] instanceof DateTime);
            $this->assertEquals($occurrences[$occurence], $date);
        }
    }

    /**
     * YEARLY rules, mostly taken from Python test suite.
     *
     * @psalm-return array<array-key, array{0:Rule, 1:non-empty-list<\DateTime>}>
     */
    public function yearlyRules(): array
    {
        return [
            'yearly' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                ),
                [
                    date_create('1997-09-02'),
                    date_create('1998-09-02'),
                    date_create('1999-09-02'),
                ],
            ],
            'yearly, interval:2' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    interval: 2,
                ),
                [
                    date_create('1997-09-02'),
                    date_create('1999-09-02'),
                    date_create('2001-09-02'),
                ],
            ],
            'yearly, bymonth:1,3' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byMonth: [1, 3],
                ),
                [
                    date_create('1998-01-02'),
                    date_create('1998-03-02'),
                    date_create('1999-01-02'),
                ],
            ],
            'yearly, bymonthday:1,3' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byMonthDay: [1, 3],
                ),
                [
                    date_create('1997-09-03'),
                    date_create('1997-10-01'),
                    date_create('1997-10-03'),
                ],
            ],
            'yearly, bymonthday:1,3, bymonth:1,3' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byMonthDay: [5, 7],
                    byMonth: [1, 3],
                ),
                [
                    date_create('1998-01-05'),
                    date_create('1998-01-07'),
                    date_create('1998-03-05'),
                ],
            ],
            'yearly, byday:TU,TH' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byDay: [
                        new WeekDayNum(WeekDay::Tuesday),
                        new WeekDayNum(WeekDay::Thursday),
                    ],
                ),
                [
                    date_create('1997-09-02'),
                    date_create('1997-09-04'),
                    date_create('1997-09-09'),
                ],
            ],
            'yearly, byday:SU' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byDay: [new WeekDayNum(WeekDay::Sunday)],
                ),
                [
                    date_create('1997-09-07'),
                    date_create('1997-09-14'),
                    date_create('1997-09-21'),
                ],
            ],
            'yearly, byday:1TU,-1TH' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byDay: [
                        WeekDay::Tuesday->num(1),
                        WeekDay::Thursday->num(-1),
                    ]
                ),
                [
                    date_create('1997-12-25'),
                    date_create('1998-01-06'),
                    date_create('1998-12-31'),
                ],
            ],
            'yearly, byday:TU,TH, bymonth:1,3' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byDay: [
                        new WeekDayNum(WeekDay::Tuesday),
                        new WeekDayNum(WeekDay::Thursday),
                    ],
                    byMonth: [1, 3],
                ),
                [
                    date_create('1998-01-01'),
                    date_create('1998-01-06'),
                    date_create('1998-01-08'),
                ],
            ],
            'yearly, byday:1TU,-1TH, bymonth:1,3' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byDay: [
                        WeekDay::Tuesday->num(1),
                        WeekDay::Thursday->num(-1),
                    ],
                    byMonth: [1, 3],
                ),
                [
                    date_create('1998-01-06'),
                    date_create('1998-01-29'),
                    date_create('1998-03-03'),
                ],
            ],
            // This is interesting because the TH(-3) ends up before the TU(3).
            'yearly, byday:3TU,-3TH, bymonth:1,3' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byDay: [
                        WeekDay::Tuesday->num(3),
                        WeekDay::Thursday->num(-3),
                    ],
                    byMonth: [1, 3],
                ),
                [
                    date_create('1998-01-15'),
                    date_create('1998-01-20'),
                    date_create('1998-03-12'),
                ],
            ],
            'yearly, byday:TU,TH, bymonthday:1,3, bymonth:1,3' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byDay: [
                        new WeekDayNum(WeekDay::Tuesday),
                        new WeekDayNum(WeekDay::Thursday),
                    ],
                    byMonthDay: [1, 3],
                    byMonth: [1, 3],
                ),
                [
                    date_create('1998-01-01'),
                    date_create('1998-03-03'),
                    date_create('2001-03-01'),
                ],
            ],
            'yearly, byyearday positive' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 4,
                    byYearDay: [1, 100, 200, 365]
                ),
                [
                    date_create('1997-12-31'),
                    date_create('1998-01-01'),
                    date_create('1998-04-10'),
                    date_create('1998-07-19'),
                ],
            ],
            'yearly, byyearday negative' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 4,
                    byYearDay: [1, 100, 200, 365],
                ),
                [
                    date_create('1997-12-31'),
                    date_create('1998-01-01'),
                    date_create('1998-04-10'),
                    date_create('1998-07-19'),
                ],
            ],
            'yearly, byyearday positive + bymonth' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 4,
                    byYearDay: [1, 100, 200, 365],
                    byMonth: [4, 7],
                ),
                [
                    date_create('1998-04-10'),
                    date_create('1998-07-19'),
                    date_create('1999-04-10'),
                    date_create('1999-07-19'),
                ],
            ],
            'yearly, byyearday negative + bymonth' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 4,
                    byYearDay: [-365, -266, -166, -1],
                    byMonth: [4, 7],
                ),
                [
                    date_create('1998-04-10'),
                    date_create('1998-07-19'),
                    date_create('1999-04-10'),
                    date_create('1999-07-19'),
                ],
            ],
            'yearly, byyearday, 29 February' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byYearDay: [60],
                ),
                [
                    date_create('1998-03-01'),
                    date_create('1999-03-01'),
                    date_create('2000-02-29'),
                ],
            ],
            'yearly, byyearday, 366th day' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byYearDay: [366],
                ),
                [
                    date_create('2000-12-31'),
                    date_create('2004-12-31'),
                    date_create('2008-12-31'),
                ],
            ],
            'yearly, byyearday, -366th day' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byYearDay: [-366],
                ),
                [
                    date_create('2000-01-01'),
                    date_create('2004-01-01'),
                    date_create('2008-01-01'),
                ],
            ],
            'yearly, byweekno:20' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byWeekNo: [20],
                ),
                [
                    date_create('1998-05-11'),
                    date_create('1998-05-12'),
                    date_create('1998-05-13'),
                ],
            ],
            // That's a nice one. The first days of week number one may be in the last year.
            'yearly, byday:MO, byweekno:1' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byDay: [new WeekDayNum(WeekDay::Monday)],
                    byWeekNo: [1],
                ),
                [
                    date_create('1997-12-29'),
                    date_create('1999-01-04'),
                    date_create('2000-01-03'),
                ],
            ],
            // Another nice test. The last days of week number 52/53 may be in the next year.
            'yearly, byday:SU, byweekno:52' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byDay: [new WeekDayNum(WeekDay::Sunday)],
                    byWeekNo: [52],
                ),
                [
                    date_create('1997-12-28'),
                    date_create('1998-12-27'),
                    date_create('2000-01-02'),
                ],
            ],
            'yearly, byday:SU, byweekno:-1' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byDay: [new WeekDayNum(WeekDay::Sunday)],
                    byWeekNo: [-1],
                ),
                [
                    date_create('1997-12-28'),
                    date_create('1999-01-03'),
                    date_create('2000-01-02'),
                ],
            ],
            'yearly, byday:MO, byweekno:53' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byDay: [new WeekDayNum(WeekDay::Monday)],
                    byWeekNo: [53],
                ),
                [
                    date_create('1998-12-28'),
                    date_create('2004-12-27'),
                    date_create('2009-12-28'),
                ],
            ],

            // todo bysetpos

            'yearly, byhour:6,18' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byHour: [6, 18],
                ),
                [
                    date_create('1997-09-02 06:00:00'),
                    date_create('1997-09-02 18:00:00'),
                    date_create('1998-09-02 06:00:00'),
                ],
            ],
            'yearly, byminute:15,30' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byMinute: [15, 30],
                ),
                [
                    date_create('1997-09-02 00:15:00'),
                    date_create('1997-09-02 00:30:00'),
                    date_create('1998-09-02 00:15:00'),
                ],
            ],
            'yearly, bysecond:10,20' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    bySecond: [10, 20],
                ),
                [
                    date_create('1997-09-02 00:00:10'),
                    date_create('1997-09-02 00:00:20'),
                    date_create('1998-09-02 00:00:10'),
                ],
            ],
            'yearly, byminute:15,30, byhour:6,18' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byMinute: [15, 30],
                    byHour: [6, 18],
                ),
                [
                    date_create('1997-09-02 06:15:00'),
                    date_create('1997-09-02 06:30:00'),
                    date_create('1997-09-02 18:15:00'),
                ],
            ],
            'yearly, bysecond:10,20, byhour:6,18' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    bySecond: [10, 20],
                    byHour: [6, 18],
                ),
                [
                    date_create('1997-09-02 06:00:10'),
                    date_create('1997-09-02 06:00:20'),
                    date_create('1997-09-02 18:00:10'),
                ],
            ],
            'yearly, bysecond:10,20, byminute:15,30' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    bySecond: [10, 20],
                    byMinute: [15, 30],
                ),
                [
                    date_create('1997-09-02 00:15:10'),
                    date_create('1997-09-02 00:15:20'),
                    date_create('1997-09-02 00:30:10'),
                ],
            ],
            'yearly, bysecond:10,20, byminute:15,30, byhour:6,18' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    bySecond: [10, 20],
                    byMinute: [15, 30],
                    byHour: [6, 18],
                ),
                [
                    date_create('1997-09-02 06:15:10'),
                    date_create('1997-09-02 06:15:20'),
                    date_create('1997-09-02 06:30:10'),
                ],
            ],
            'yearly, byhour:6,18, bymonthday:15, bysecpot:3,-3' => [
                new Rule(
                    freq: Freq::Yearly,
                    count: 3,
                    byHour: [6, 18],
                    byMonthDay: [15],
                    bySetPos: [3, -3],
                ),
                [
                    date_create('1997-11-15 18:00:00'),
                    date_create('1998-02-15 06:00:00'),
                    date_create('1998-11-15 18:00:00'),
                ],
            ],
        ];
    }

    /**
     * MONTHY rules, mostly taken from the Python test suite
     *
     * @psalm-return array<array-key, array{0:Rule, 1:non-empty-list<\DateTime>}>
     */
    public function monthlyRules(): array
    {
        return [
            'monthly' => [
                new Rule(freq: Freq::Monthly, count: 3),
                [
                    date_create('1997-09-02'),
                    date_create('1997-10-02'),
                    date_create('1997-11-02'),
                ],
            ],
            'monthly, interval:2' => [
                new Rule(freq: Freq::Monthly, count: 3, interval: 2),
                [
                    date_create('1997-09-02'),
                    date_create('1997-11-02'),
                    date_create('1998-01-02'),
                ],
            ],
            'monthly, 1.5 years' => [
                new Rule(freq: Freq::Monthly, count: 3, interval: 18),
                [
                    date_create('1997-09-02'),
                    date_create('1999-03-02'),
                    date_create('2000-09-02'),
                ],
            ],
            'monthly, bymonth:1,3' => [
                new Rule(freq: Freq::Monthly, count: 3, byMonth: [1, 3]),
                [
                    date_create('1998-01-02'),
                    date_create('1998-03-02'),
                    date_create('1999-01-02'),
                ],
            ],
            'monthly, bymonthday:1,3' => [
                new Rule(freq: Freq::Monthly, count: 3, byMonthDay: [1, 3]),
                [
                    date_create('1997-09-03'),
                    date_create('1997-10-01'),
                    date_create('1997-10-03'),
                ],
            ],
            'monthly, bymonthday:4,7, bymonth:1,3' => [
                new Rule(freq: Freq::Monthly, count: 3, byMonthDay: [5, 7], byMonth: [1, 3]),
                [
                    date_create('1998-01-05'),
                    date_create('1998-01-07'),
                    date_create('1998-03-05'),
                ],
            ],
            'monthly, byday:TU,TH' => [
                new Rule(freq: Freq::Monthly, count: 3, byDay: [
                    new WeekDayNum(WeekDay::Tuesday),
                    new WeekDayNum(WeekDay::Thursday),
                ]),
                [
                    date_create('1997-09-02'),
                    date_create('1997-09-04'),
                    date_create('1997-09-09'),
                ],
            ],
            // Third Monday of the month
            'monthly, byday:3MO' => [
                new Rule(freq: Freq::Monthly, count: 3, byDay: [
                    new WeekDayNum(WeekDay::Monday, 3),
                ]),
                [
                    date_create('1997-09-15'),
                    date_create('1997-10-20'),
                    date_create('1997-11-17'),
                ],
            ],
            'monthly, byday:1TU,-1TH' => [
                new Rule(freq: Freq::Monthly, count: 3, byDay: [
                    new WeekDayNum(WeekDay::Tuesday, 1),
                    new WeekDayNum(WeekDay::Thursday, -1),
                ]),
                [
                    date_create('1997-09-02'),
                    date_create('1997-09-25'),
                    date_create('1997-10-07'),
                ],
            ],
            'monthly, byday:3TU,-3TH' => [
                new Rule(freq: Freq::Monthly, count: 3, byDay: [
                    new WeekDayNum(WeekDay::Tuesday, 3),
                    new WeekDayNum(WeekDay::Thursday, -3),
                ]),
                [
                    date_create('1997-09-11'),
                    date_create('1997-09-16'),
                    date_create('1997-10-16'),
                ],
            ],
            'monthly, byday:TU,TH, bymonth:1,3' => [
                new Rule(freq: Freq::Monthly, count: 3, byDay: [
                    new WeekDayNum(WeekDay::Tuesday),
                    new WeekDayNum(WeekDay::Thursday),
                ], byMonth: [1, 3]),
                [
                    date_create('1998-01-01'),
                    date_create('1998-01-06'),
                    date_create('1998-01-08'),
                ],
            ],
            'monthly, byday:1TU,-1TH, bymonth:1,3' => [
                new Rule(freq: Freq::Monthly, count: 3, byDay: [
                    new WeekDayNum(WeekDay::Tuesday, 1),
                    new WeekDayNum(WeekDay::Thursday, -1),
                ], byMonth: [1, 3]),
                [
                    date_create('1998-01-06'),
                    date_create('1998-01-29'),
                    date_create('1998-03-03'),
                ],
            ],
            'monthly, byday:3TU,-3TH, bymonth:1,3' => [
                new Rule(freq: Freq::Monthly, count: 3, byDay: [
                    new WeekDayNum(WeekDay::Tuesday, 3),
                    new WeekDayNum(WeekDay::Thursday, -3),
                ], byMonth: [1, 3]),
                [
                    date_create('1998-01-15'),
                    date_create('1998-01-20'),
                    date_create('1998-03-12'),
                ],
            ],
            'monthly, byday:TU,TH, bymonthday:1,3' => [
                new Rule(freq: Freq::Monthly, count: 3, byDay: [
                    new WeekDayNum(WeekDay::Tuesday),
                    new WeekDayNum(WeekDay::Thursday),
                ], byMonthDay: [1, 3]),
                [
                    date_create('1998-01-01'),
                    date_create('1998-02-03'),
                    date_create('1998-03-03'),
                ],
            ],
            'monthly, byday:TU,TH, bymonthday:1,3, bymonth:1,3' => [
                new Rule(freq: Freq::Monthly, count: 3, byDay: [
                    new WeekDayNum(WeekDay::Tuesday),
                    new WeekDayNum(WeekDay::Thursday),
                ], byMonthDay: [1, 3], byMonth: [1, 3]),
                [
                    date_create('1998-01-01'),
                    date_create('1998-03-03'),
                    date_create('2001-03-01'),
                ],
            ],

            // last workday of the month
            'monthly, last workday of the month' => [
                new Rule(freq: Freq::Monthly, count: 3, byDay: [
                    new WeekDayNum(WeekDay::Monday),
                    new WeekDayNum(WeekDay::Tuesday),
                    new WeekDayNum(WeekDay::Wednesday),
                    new WeekDayNum(WeekDay::Thursday),
                    new WeekDayNum(WeekDay::Friday),
                ], bySetPos: [-1]),
                [
                    date_create('1997-09-30'),
                    date_create('1997-10-31'),
                    date_create('1997-11-28'),
                ],
            ],

            // first working day of the month, or previous Friday
            // see http://stackoverflow.com/questions/38170676/recurring-calendar-event-on-first-of-the-month/38314515
            'monthly, first working day of the month or previous Friday #1' => [
                new Rule(freq: Freq::Monthly, count: 3, byDay: [
                    new WeekDayNum(WeekDay::Monday, 1),
                    new WeekDayNum(WeekDay::Tuesday, 1),
                    new WeekDayNum(WeekDay::Wednesday, 1),
                    new WeekDayNum(WeekDay::Thursday, 1),
                    new WeekDayNum(WeekDay::Friday, 1),
                    new WeekDayNum(WeekDay::Friday, -1),
                ], byMonthDay: [1, -1, -2]),
                [
                    date_create('1997-10-01'),
                    date_create('1997-10-31'),
                    date_create('1997-12-01'),
                ],
            ],
            'monthly, first working day of the month or previous Friday #2' => [
                new Rule(freq: Freq::Monthly, count: 3, byDay: [
                    new WeekDayNum(WeekDay::Monday, 1),
                    new WeekDayNum(WeekDay::Tuesday, 1),
                    new WeekDayNum(WeekDay::Wednesday, 1),
                    new WeekDayNum(WeekDay::Thursday, 1),
                    new WeekDayNum(WeekDay::Friday),
                ], byMonthDay: [1, -1, -2]),
                [
                    date_create('1997-10-01'),
                    date_create('1997-10-31'),
                    date_create('1997-12-01'),
                ],
            ],

            'monthly, byhour:6,18' => [
                new Rule(freq: Freq::Monthly, count: 3, byHour: [6, 18]),
                [
                    date_create('1997-09-02 06:00:00'),
                    date_create('1997-09-02 18:00:00'),
                    date_create('1997-10-02 06:00:00'),
                ],
            ],
            'monthly, byminute:6,18' => [
                new Rule(freq: Freq::Monthly, count: 3, byMinute: [6, 18]),
                [
                    date_create('1997-09-02 00:06:00'),
                    date_create('1997-09-02 00:18:00'),
                    date_create('1997-10-02 00:06:00'),
                ],
            ],
            'monthly, bysecond:6,18' => [
                new Rule(freq: Freq::Monthly, count: 3, bySecond: [6, 18]),
                [
                    date_create('1997-09-02 00:00:06'),
                    date_create('1997-09-02 00:00:18'),
                    date_create('1997-10-02 00:00:06'),
                ],
            ],
            'monthly, byminute:6,18, byhour:6,18' => [
                new Rule(freq: Freq::Monthly, count: 3, byMinute: [6, 18], byHour: [6, 18]),
                [
                    date_create('1997-09-02 06:06:00'),
                    date_create('1997-09-02 06:18:00'),
                    date_create('1997-09-02 18:06:00'),
                ],
            ],
            'monthly, bysecond:6,18, byhour:6,18' => [
                new Rule(freq: Freq::Monthly, count: 3, bySecond: [6, 18], byHour: [6, 18]),
                [
                    date_create('1997-09-02 06:00:06'),
                    date_create('1997-09-02 06:00:18'),
                    date_create('1997-09-02 18:00:06'),
                ],
            ],
            'monthly, bysecond:6,18, byminute:6,18' => [
                new Rule(freq: Freq::Monthly, count: 3, bySecond: [6, 18], byMinute: [6, 18]),
                [
                    date_create('1997-09-02 00:06:06'),
                    date_create('1997-09-02 00:06:18'),
                    date_create('1997-09-02 00:18:06'),
                ],
            ],
            'monthly, bysecond:6,18, byminute:6,18, byhour:6,18' => [
                new Rule(freq: Freq::Monthly, count: 3, bySecond: [6, 18], byMinute: [6, 18], byHour: [6, 18]),
                [
                    date_create('1997-09-02 06:06:06'),
                    date_create('1997-09-02 06:06:18'),
                    date_create('1997-09-02 06:18:06'),
                ],
            ],
            'monthly, byhour:6,18, bymonthday:13,17, bysetpos:3,-3' => [
                new Rule(freq: Freq::Monthly, count: 3, byHour: [6, 18], byMonthDay: [13, 17], bySetPos: [3, -3]),
                [
                    date_create('1997-09-13 18:00'),
                    date_create('1997-09-17 06:00'),
                    date_create('1997-10-13 18:00'),
                ],
            ],
            // avoid duplicates
            'monthly, byhour:6,18, bymonth:13,17, bysetpos:3,3,-3' => [
                new Rule(freq: Freq::Monthly, count: 3, byHour: [6, 18], byMonthDay: [13, 17], bySetPos: [3, 3, -3]),
                [
                    date_create('1997-09-13 18:00'),
                    date_create('1997-09-17 06:00'),
                    date_create('1997-10-13 18:00'),
                ],
            ],
            'monthly, byhour:6,18, bymonthday:13,17, bysetpos:4,-1' => [
                new Rule(freq: Freq::Monthly, count: 3, byHour: [6, 18], byMonthDay: [13, 17], bySetPos: [4, -1]),
                [
                    date_create('1997-09-17 18:00'),
                    date_create('1997-10-17 18:00'),
                    date_create('1997-11-17 18:00'),
                ],
            ],
        ];
    }
}
