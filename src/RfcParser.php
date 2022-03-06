<?php declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

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
use Brzuchal\RecurrenceRule\ValueObject\MonthDayNum;
use Brzuchal\RecurrenceRule\ValueObject\MonthNum;
use Brzuchal\RecurrenceRule\ValueObject\WeekDayNum;
use Brzuchal\RecurrenceRule\ValueObject\WeekNum;
use Brzuchal\RecurrenceRule\ValueObject\YearDayNum;
use DateTimeImmutable;

// Do not be silent! #StopWar 🇺🇦 #StandWithUkraine #StopPutin
/**
 * Source of definition {@see https://www.rfc-editor.org/rfc/rfc5545.html#section-3.3.10}
 * Format Definition: This value type is defined by the following
 * notation:
 *   recur              = recur-rule-part *( ";" recur-rule-part )
 *                      ;
 *                      ; The rule parts are not ordered in any
 *                      ; particular sequence.
 *                      ;
 *                      ; The FREQ rule part is REQUIRED,
 *                      ; but MUST NOT occur more than once.
 *                      ;
 *                      ; The UNTIL or COUNT rule parts are OPTIONAL,
 *                      ; but they MUST NOT occur in the same ’recur’.
 *                      ; The other rule parts are OPTIONAL,
 *                      ; but MUST NOT occur more than once.
 *   recur-rule-part    = ( "FREQ" "=" freq )
 *                      / ( "UNTIL" "=" enddate )
 *                      / ( "COUNT" "=" 1*DIGIT )
 *                      / ( "INTERVAL" "=" 1*DIGIT )
 *                      / ( "BYSECOND" "=" byseclist )
 *                      / ( "BYMINUTE" "=" byminlist )
 *                      / ( "BYHOUR" "=" byhrlist )
 *                      / ( "BYDAY" "=" bywdaylist )
 *                      / ( "BYMONTHDAY" "=" bymodaylist )
 *                      / ( "BYYEARDAY" "=" byyrdaylist )
 *                      / ( "BYWEEKNO" "=" bywknolist )
 *                      / ( "BYMONTH" "=" bymolist )
 *                      / ( "BYSETPOS" "=" bysplist )
 *                      / ( "WKST" "=" weekday )
 *   freq               = "SECONDLY" / "MINUTELY" / "HOURLY" / "DAILY"
 *                      / "WEEKLY" / "MONTHLY" / "YEARLY"
 *   enddate            = date / date-time
 *   byseclist          = ( seconds *("," seconds) )
 *   seconds            = 1*2DIGIT ;0 to 60
 *   byminlist          = ( minutes *("," minutes) )
 *   minutes            = 1*2DIGIT ;0 to 59
 *   byhrlist           = ( hour *("," hour) )
 *   hour               = 1*2DIGIT ;0 to 23
 *   bywdaylist         = ( weekdaynum *("," weekdaynum) )
 *   weekdaynum         = [[plus / minus] ordwk] weekday
 *   plus               = "+"
 *   minus              = "-"
 *   ordwk              = 1*2DIGIT ;1 to 53
 *   weekday            = "SU" / "MO" / "TU" / "WE" / "TH" / "FR" / "SA"
 *                      ;Corresponding to SUNDAY, MONDAY, TUESDAY, WEDNESDAY, THURSDAY,
 *                      ;FRIDAY, and SATURDAY days of the week.
 *   bymodaylist        = ( monthdaynum *("," monthdaynum) )
 *   monthdaynum        = [plus / minus] ordmoday
 *   ordmoday           = 1*2DIGIT ;1 to 31
 *   byyrdaylist        = ( yeardaynum *("," yeardaynum) )
 *   yeardaynum         = [plus / minus] ordyrday
 *   ordyrday           = 1*3DIGIT ;1 to 366
 *   bywknolist         = ( weeknum *("," weeknum) )
 *   weeknum            = [plus / minus] ordwk
 *   bymolist           = ( monthnum *("," monthnum) )
 *   monthnum           = 1*2DIGIT ;1 to 12
 *   bysplist           = ( setposday *("," setposday) )
 *   setposday          = yeardaynum
 */
final class RfcParser
{
    /**
     * @psalm-return list<PartialRule>
     * @throws RfcParserSyntaxError
     */
    public static function fromString(string $rule): iterable
    {
        $offset = 0;
        $length = \strlen($rule);
        $parts = [];
        while ($offset < $length) {
            $partialRuleDelimiter = \stripos($rule, ';', $offset);
            $partialRule = \substr($rule, $offset, $partialRuleDelimiter ? $partialRuleDelimiter - $offset : null);
            $parts[] = match ($partialRule[0]) {
                'F' => self::tryParseFreq($partialRule),
                'U' => self::tryParseUntil($partialRule),
                'C' => self::tryParseCount($partialRule),
                'I' => self::tryParseInterval($partialRule),
                'B' => self::tryParseByRules($partialRule),
                'W' => self::tryParseWkst($partialRule),
            };
            $offset += \strlen($partialRule) + 1;
        }

        return $parts;
    }

    /**
     * @throws RfcParserSyntaxError
     */
    protected static function tryParseFreq(string $partialRule): PartialRule
    {
        if (\strpos($partialRule, 'FREQ=') !== 0) {
            throw RfcParserSyntaxError::create($partialRule);
        }

        return new FreqRule(Freq::from(\substr($partialRule, 5)));
    }

    /**
     * @throws RfcParserSyntaxError
     */
    private static function tryParseUntil(string $partialRule): PartialRule
    {
        if (\strpos($partialRule, 'UNTIL=') !== 0) {
            throw RfcParserSyntaxError::create($partialRule);
        }

        $dateTimeString = \substr($partialRule, 6);

        /** @psalm-suppress PossiblyFalseReference */
        return new UntilRule(match (\strlen($dateTimeString)) {
            8 => DateTimeImmutable::createFromFormat('Ymd', $dateTimeString)->setTime(23, 59, 59),
            15 => DateTimeImmutable::createFromFormat('Ymd\THis', $dateTimeString),
            default => throw RfcParserSyntaxError::create("UNTIL={$partialRule}", 'unsupported date format'),
        });
    }

    /**
     * @throws RfcParserSyntaxError
     */
    private static function tryParseCount(string $partialRule): PartialRule
    {
        return new CountRule(self::tryParsePositiveInt('COUNT=', $partialRule));
    }

    /**
     * @throws RfcParserSyntaxError
     */
    private static function tryParseInterval(string $partialRule): PartialRule
    {
        return new IntervalRule(self::tryParsePositiveInt('INTERVAL=', $partialRule));
    }

    /**
     * @psalm-return positive-int
     * @throws RfcParserSyntaxError
     */
    private static function tryParsePositiveInt(string $ruleName, string $partialRule): int
    {
        if (\strpos($partialRule, $ruleName) !== 0) {
            throw RfcParserSyntaxError::create("{$ruleName}={$partialRule}");
        }

        $numberString = \substr($partialRule, \strlen($ruleName));
        if (!\is_numeric($numberString) || \intval($numberString) < 0) {
            throw RfcParserSyntaxError::create("{$ruleName}={$partialRule}", "expected positive integer, instead: {$numberString} given");
        }

        $number = \intval($numberString);
        \assert($number > 0);

        return $number;
    }

    /**
     * @throws RfcParserSyntaxError
     */
    private static function tryParseByRules(string $partialRule): PartialRule
    {
        $assignOffset = \strpos($partialRule, '=');
        if ($assignOffset === false) {
            throw RfcParserSyntaxError::create($partialRule, 'expected value assignment was not found');
        }

        $byName = \substr($partialRule, 0, $assignOffset);
        $value = \substr($partialRule, ++$assignOffset);

        return match ($byName) {
            'BYSECOND' => self::tryParseBySecond($value),
            'BYMINUTE' => self::tryParseByMinute($value),
            'BYHOUR' => self::tryParseByHour($value),
            'BYDAY' => self::tryParseByDay($value),
            'BYMONTHDAY' => self::tryParseByMonthDay($value),
            'BYYEARDAY' => self::tryParseByYearDay($value),
            'BYWEEKNO' => self::tryParseByWeekNo($value),
            'BYMONTH' => self::tryParseMonth($value),
            'BYSETPOS' => self::tryParseSetPos($value),
        };
    }

    /**
     * @throws RfcParserSyntaxError
     */
    private static function tryParseBySecond(string $value): PartialRule
    {
        return new BySecondRule(self::tryParseLimitedNumberList('BYSECOND', $value, 60));
    }

    /**
     * @throws RfcParserSyntaxError
     */
    private static function tryParseByMinute(string $value): PartialRule
    {
        return new ByMinuteRule(self::tryParseLimitedNumberList('BYMINUTE', $value, 60));
    }

    /**
     * @throws RfcParserSyntaxError
     */
    private static function tryParseByHour(string $value): PartialRule
    {
        return new ByHourRule(self::tryParseLimitedNumberList('BYHOUR', $value, 23));
    }

    private static function tryParseByDay(string $value): PartialRule
    {
        return new ByDayRule(\array_map(self::tryParseByWeekDayNum(...), \explode(',', $value)));
    }

    /**
     * @throws RfcParserSyntaxError
     */
    private static function tryParseByWeekDayNum(string $weekDayNum): WeekDayNum
    {
        $length = \strlen($weekDayNum);
        if ($length < 2) {
            throw RfcParserSyntaxError::create('BYDAY=' . $weekDayNum, 'expected at least valid week day name');
        }

        $negative = match ($weekDayNum[0]) {
            '+' => false,
            '-' => true,
            default => null,
        };
        $weekDay = WeekDay::from(\substr($weekDayNum, -2, 2));
        if ($length === 2 || ($negative !== null && $length === 3)) {
            return new WeekDayNum($weekDay, negative: $negative);
        }

        $ordWeekNumber = \substr($weekDayNum, $negative !== null ? 1 : 0, -2);
        if (!\is_numeric($ordWeekNumber) || \intval($ordWeekNumber) < 1 || \intval($ordWeekNumber) > 53) {
            throw RfcParserSyntaxError::create('BYDAY=' . $weekDayNum, 'expected valid week ordinal number');
        }

        return new WeekDayNum($weekDay, \intval($ordWeekNumber), $negative);
    }

    public static function tryParseByMonthDay(string $value): PartialRule
    {
        return new ByMonthDayRule(\array_map(self::tryParseMonthDayNum(...), \explode(',', $value)));
    }

    /**
     * @throws RfcParserSyntaxError
     */
    private static function tryParseMonthDayNum(string $monthDayNum): MonthDayNum
    {
        [$negative, $monthDayNumber] = self::tryParseLimitedNumberWithSign(
            'BYMONTHDAY',
            $monthDayNum,
            'expected valid number of day in month',
            31,
        );

        return new MonthDayNum(\intval($monthDayNumber), $negative);
    }

    private static function tryParseByYearDay(string $value): PartialRule
    {
        return new ByYearDayRule(\array_map(self::tryParseYearDayNum(...), \explode(',', $value)));
    }

    /**
     * @throws RfcParserSyntaxError
     */
    private static function tryParseYearDayNum(string $yearDayNum): YearDayNum
    {
        [$negative, $yearDayNumber] = self::tryParseLimitedNumberWithSign(
            'BYYEARDAY',
            $yearDayNum,
            'expected valid number of day in year',
            366,
        );

        return new YearDayNum(\intval($yearDayNumber), $negative);
    }

    private static function tryParseByWeekNo(string $value): PartialRule
    {
        return new ByWeekNoRule(\array_map(self::tryParseWeekNum(...), \explode(',', $value)));
    }

    /**
     * @throws RfcParserSyntaxError
     */
    private static function tryParseWeekNum(string $weekNum): WeekNum
    {
        [$negative, $weekNumber] = self::tryParseLimitedNumberWithSign(
            'BYWEEKNO',
            $weekNum,
            'expected valid number of week in year',
            53,
        );

        return new WeekNum(\intval($weekNumber), $negative);
    }

    private static function tryParseMonth(string $value): PartialRule
    {
        return new ByMonthRule(\array_map(self::tryParseMonthNum(...), \explode(',', $value)));
    }

    /**
     * @throws RfcParserSyntaxError
     */
    private static function tryParseMonthNum(string $monthNum): MonthNum
    {
        [$negative, $monthNumber] = self::tryParseLimitedNumberWithSign(
            'BYMONTH',
            $monthNum,
            'expected valid number of month in year',
            12,
        );

        return new MonthNum(\intval($monthNumber), $negative);
    }

    private static function tryParseSetPos(string $value): PartialRule
    {
        return new BySetPosRule(\array_map(self::tryParseYearDayNum(...), \explode(',', $value)));
    }

    /**
     * @throws RfcParserSyntaxError
     */
    private static function tryParseWkst(string $partialRule): PartialRule
    {
        if (\strpos($partialRule, 'WKST=') !== 0) {
            throw RfcParserSyntaxError::create($partialRule);
        }

        return new WkstRule(WeekDay::from(\substr($partialRule, 5)));
    }

    /**
     * @psalm-suppress MoreSpecificReturnType,LessSpecificReturnStatement
     * @psalm-return list<positive-int>
     * @throws RfcParserSyntaxError
     */
    private static function tryParseLimitedNumberList(string $ruleName, string $value, int $max): array
    {
        $list = \explode(',', $value);
        $errors = \array_sum(\array_map(
            static fn (string $number) => !\is_numeric($number) || \intval($number) < 0 || \intval($number) > $max,
            $list,
        ));
        if ($errors > 0) {
            throw RfcParserSyntaxError::create("{$ruleName}={$value}", 'expected comma separated list of numbers between {$min}-{$max} range');
        }

        return \array_map(\intval(...), $list);
    }

    /**
     * @throws RfcParserSyntaxError
     */
    private static function tryParseLimitedNumberWithSign(
        string $ruleName,
        string $value,
        string $extendedError,
        int $max,
    ): array {
        if (empty($value) || $value === '+' || $value === '-') {
            throw RfcParserSyntaxError::create("{$ruleName}={$value}", $extendedError);
        }

        $negative = match ($value[0]) {
            '+' => false,
            '-' => true,
            default => null,
        };
        $number = \substr($value, $negative !== null ? 1 : 0);
        if (!\is_numeric($number) || \intval($number) < 1 || \intval($number) > $max) {
            throw RfcParserSyntaxError::create("{$ruleName}={$value}", $extendedError);
        }

        return [$negative, $number];
    }
}
