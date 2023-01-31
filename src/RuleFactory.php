<?php

declare(strict_types=1);

namespace Brzuchal\RecurrenceRule;

use DateTimeImmutable;
use UnexpectedValueException;

use function sprintf;

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
 *
 * @psalm-import-type second from Rule
 * @psalm-import-type minute from Rule
 * @psalm-import-type hour from Rule
 * @psalm-import-type monthday from Rule
 * @psalm-import-type yearday from Rule
 * @psalm-import-type weekno from Rule
 * @psalm-import-type monthno from Rule
 */
final class RuleFactory
{
    /**
     * @throws UnexpectedValueException
     */
    public static function fromString(string $rule): Rule
    {
        $freqOffset = \stripos($rule, 'FREQ=');
        if ($freqOffset === false) {
            throw new UnexpectedValueException('Missing FREQ rule');
        }

        $freqRuleDelimiter = \stripos($rule, ';', $freqOffset);
        $freqRule = \substr($rule, $freqOffset, $freqRuleDelimiter ? $freqRuleDelimiter - $freqOffset : null);
        $builder = new RuleBuilder(self::tryParseFreq($freqRule));

        $offset = 0;
        $length = \strlen($rule);
        while ($offset < $length) {
            $partialRuleDelimiter = \stripos($rule, ';', $offset);
            $partialRule = \substr($rule, $offset, $partialRuleDelimiter ? $partialRuleDelimiter - $offset : null);
            switch ($partialRule[0]) {
                case 'F':
                    // do nothing, freq already handled
                    break;
                case 'U':
                    $builder->until(self::tryParseUntil($partialRule));
                    break;
                case 'C':
                    $builder->count(self::tryParseCount($partialRule));
                    break;
                case 'I':
                    $builder->interval(self::tryParseInterval($partialRule));
                    break;
                case 'B':
                    self::tryParseByRules($builder, $partialRule);
                    break;
                case 'W':
                    $builder->workWeekStart(self::tryParseWkst($partialRule));
                    break;
            }

            $offset += \strlen($partialRule) + 1;
        }

        return $builder->build();
    }

    /**
     * @throws UnexpectedValueException
     */
    protected static function tryParseFreq(string $partialRule): Freq
    {
        if (\strpos($partialRule, 'FREQ=') !== 0) {
            throw self::error($partialRule);
        }

        return Freq::from(\substr($partialRule, 5));
    }

    /**
     * @throws UnexpectedValueException
     */
    private static function tryParseUntil(string $partialRule): DateTimeImmutable
    {
        if (\strpos($partialRule, 'UNTIL=') !== 0) {
            throw self::error($partialRule);
        }

        $dateTimeString = \substr($partialRule, 6);

        /** @psalm-suppress PossiblyFalseReference */
        $dateTime = match (\strlen($dateTimeString)) {
            8 => DateTimeImmutable::createFromFormat('Ymd', $dateTimeString)->setTime(23, 59, 59),
            15 => DateTimeImmutable::createFromFormat('Ymd\THis', $dateTimeString),
            default => throw self::error(sprintf('UNTIL=%s', $partialRule), 'unsupported date format'),
        };

        if (! ($dateTime instanceof DateTimeImmutable)) {
            throw self::error(sprintf('UNTIL=%s', $partialRule), 'unsupported date format');
        }

        return $dateTime;
    }

    /**
     * @psalm-return positive-int
     *
     * @throws UnexpectedValueException
     */
    private static function tryParseCount(string $partialRule): int
    {
        return self::tryParsePositiveInt('COUNT=', $partialRule);
    }

    /**
     * @psalm-return positive-int
     *
     * @throws UnexpectedValueException
     */
    private static function tryParseInterval(string $partialRule): int
    {
        return self::tryParsePositiveInt('INTERVAL=', $partialRule);
    }

    /**
     * @psalm-return positive-int
     *
     * @throws UnexpectedValueException
     */
    private static function tryParsePositiveInt(string $ruleName, string $partialRule): int
    {
        if (\strpos($partialRule, $ruleName) !== 0) {
            throw self::error(sprintf('%s=%s', $ruleName, $partialRule));
        }

        $numberString = \substr($partialRule, \strlen($ruleName));
        if (! \is_numeric($numberString) || \intval($numberString) < 0) {
            throw self::error(
                sprintf('%s=%s', $ruleName, $partialRule),
                sprintf('expected positive integer, instead: %s given', $numberString),
            );
        }

        $number = \intval($numberString);
        \assert($number > 0);

        return $number;
    }

    /**
     * @throws UnexpectedValueException
     */
    private static function tryParseByRules(RuleBuilder $builder, string $partialRule): void
    {
        $assignOffset = \strpos($partialRule, '=');
        if ($assignOffset === false) {
            throw self::error($partialRule, 'expected value assignment was not found');
        }

        $byName = \substr($partialRule, 0, $assignOffset);
        $value = \substr($partialRule, ++$assignOffset);

        switch ($byName) {
            case 'BYSECOND':
                $builder->bySecond(...self::tryParseBySecond($value));
                break;
            case 'BYMINUTE':
                $builder->byMinute(...self::tryParseByMinute($value));
                break;
            case 'BYHOUR':
                $builder->byHour(...self::tryParseByHour($value));
                break;
            case 'BYDAY':
                $builder->byDay(...self::tryParseByDay($value));
                break;
            case 'BYMONTHDAY':
                $builder->byMonthDay(...self::tryParseByMonthDay($value));
                break;
            case 'BYYEARDAY':
                $builder->byYearDay(...self::tryParseByYearDay($value));
                break;
            case 'BYWEEKNO':
                $builder->byWeekNo(...self::tryParseByWeekNo($value));
                break;
            case 'BYMONTH':
                $builder->byMonth(...self::tryParseMonth($value));
                break;
            case 'BYSETPOS':
                $builder->bySetPos(...self::tryParseSetPos($value));
                break;
        }
    }

    /**
     * @psalm-return non-empty-list<second>
     *
     * @throws UnexpectedValueException
     */
    private static function tryParseBySecond(string $value): array
    {
        /** @psalm-var non-empty-list<second> $seconds */
        $seconds = self::tryParseLimitedNumberList('BYSECOND', $value, 60);
        // phpcs:ignore
        \assert(\array_product(\array_map(\is_int(...), $seconds)) === 1);

        return $seconds;
    }

    /**
     * @psalm-return non-empty-list<minute>
     *
     * @throws UnexpectedValueException
     */
    private static function tryParseByMinute(string $value): array
    {
        /** @psalm-var non-empty-list<minute> $minutes */
        $minutes = self::tryParseLimitedNumberList('BYMINUTE', $value, 60);
        // phpcs:ignore
        \assert(\array_product(\array_map(\is_int(...), $minutes)) === 1);

        return $minutes;
    }

    /**
     * @psalm-return non-empty-list<hour>
     *
     * @throws UnexpectedValueException
     */
    private static function tryParseByHour(string $value): array
    {
        /** @psalm-var non-empty-list<hour> $hours */
        $hours = self::tryParseLimitedNumberList('BYHOUR', $value, 23);
        // phpcs:ignore
        \assert(\array_product(\array_map(\is_int(...), $hours)) === 1);

        return $hours;
    }

    /**
     * @psalm-return non-empty-list<WeekDayNum>
     */
    private static function tryParseByDay(string $value): array
    {
        return \array_map(self::tryParseByWeekDayNum(...), \explode(',', $value));
    }

    /**
     * @throws UnexpectedValueException
     */
    private static function tryParseByWeekDayNum(string $weekDayNum): WeekDayNum
    {
        $length = \strlen($weekDayNum);
        if ($length < 2) {
            throw self::error(
                sprintf('BYDAY=%s', $weekDayNum),
                'expected at least valid week day name',
            );
        }

        $weekDay = WeekDay::from(\substr($weekDayNum, -2, 2));
        if ($length === 2) {
            return new WeekDayNum($weekDay);
        }

        $negative = match ($weekDayNum[0]) {
            '+' => false,
            '-' => true,
            default => null,
        };
        $ordWeekNumber = \substr($weekDayNum, $negative !== null ? 1 : 0, -2);
        if (
            ! \is_numeric($ordWeekNumber) ||
            \intval($ordWeekNumber) < -53 ||
            \intval($ordWeekNumber) > 53 ||
            \intval($ordWeekNumber) === 0
        ) {
            throw self::error(
                sprintf('BYDAY=%s', $weekDayNum),
                'expected valid week ordinal number',
            );
        }

        $ordWeek = \intval($ordWeekNumber);

        return new WeekDayNum($weekDay, $negative ? -$ordWeek : $ordWeek);
    }

    /**
     * @psalm-return non-empty-list<monthday>
     */
    public static function tryParseByMonthDay(string $value): array
    {
        return \array_map(self::tryParseMonthDayNum(...), \explode(',', $value));
    }

    /**
     * @psalm-return monthday
     *
     * @throws UnexpectedValueException
     */
    private static function tryParseMonthDayNum(string $monthDayNum): int
    {
        /** @psalm-var monthday $monthDay */
        $monthDay = self::tryParseLimitedNumberWithSign(
            'BYMONTHDAY',
            $monthDayNum,
            'expected valid number of day in month',
            31,
        );
        RuleValidator::assertMonthDayNum($monthDay);

        return $monthDay;
    }

    /**
     * @psalm-return non-empty-list<yearday>
     */
    private static function tryParseByYearDay(string $value): array
    {
        return \array_map(self::tryParseYearDayNum(...), \explode(',', $value));
    }

    /**
     * @psalm-return yearday
     *
     * @throws UnexpectedValueException
     */
    private static function tryParseYearDayNum(string $yearDayNum): int
    {
        /** @psalm-var yearday $yearDay */
        $yearDay = self::tryParseLimitedNumberWithSign(
            'BYYEARDAY',
            $yearDayNum,
            'expected valid number of day in year',
            366,
        );
        RuleValidator::assertYearDayNum($yearDay);

        return $yearDay;
    }

    /**
     * @psalm-return non-empty-list<weekno>
     */
    private static function tryParseByWeekNo(string $value): array
    {
        return \array_map(self::tryParseWeekNum(...), \explode(',', $value));
    }

    /**
     * @psalm-return weekno
     *
     * @throws UnexpectedValueException
     */
    private static function tryParseWeekNum(string $weekNum): int
    {
        /** @psalm-var weekno $week */
        $week = self::tryParseLimitedNumberWithSign(
            'BYWEEKNO',
            $weekNum,
            'expected valid number of week in year',
            53,
        );
        RuleValidator::assertWeekNum($week);

        return $week;
    }

    /**
     * @psalm-return non-empty-list<monthno>
     */
    private static function tryParseMonth(string $value): array
    {
        return \array_map(self::tryParseMonthNum(...), \explode(',', $value));
    }

    /**
     * @psalm-return monthno
     *
     * @throws UnexpectedValueException
     */
    private static function tryParseMonthNum(string $monthNum): int
    {
        /** @psalm-var monthno $month */
        $month = self::tryParseLimitedNumberWithSign(
            'BYMONTH',
            $monthNum,
            'expected valid number of month in year',
            12,
        );
        RuleValidator::assertMonthNum($month);

        return $month;
    }

    /**
     * @psalm-return non-empty-list<yearday>
     */
    private static function tryParseSetPos(string $value): array
    {
        return \array_map(self::tryParseYearDayNum(...), \explode(',', $value));
    }

    /**
     * @throws UnexpectedValueException
     */
    private static function tryParseWkst(string $partialRule): WeekDay
    {
        if (\strpos($partialRule, 'WKST=') !== 0) {
            throw self::error($partialRule);
        }

        return WeekDay::from(\substr($partialRule, 5));
    }

    /**
     * @psalm-param T $max
     *
     * @psalm-return non-empty-list<int<1,T>>
     *
     * @throws UnexpectedValueException
     *
     * @template T as positive-int
     * @psalm-suppress MoreSpecificReturnType,LessSpecificReturnStatement
     */
    private static function tryParseLimitedNumberList(string $ruleName, string $value, int $max): array
    {
        $list = \explode(',', $value);
        $errors = \array_sum(\array_map(
            static fn (string $number) => ! \is_numeric($number) || \intval($number) < 0 || \intval($number) > $max,
            $list,
        ));
        if ($errors > 0) {
            throw self::error(
                sprintf('%s=%s', $ruleName, $value),
                sprintf('expected comma separated list of numbers between 0-%d range', $max),
            );
        }

        // phpcs:ignore
        return \array_map(\intval(...), $list);
    }

    /**
     * @throws UnexpectedValueException
     */
    private static function tryParseLimitedNumberWithSign(
        string $ruleName,
        string $value,
        string $extendedError,
        int $max,
    ): int {
        if (empty($value) || $value === '+' || $value === '-') {
            throw self::error(
                sprintf('%s=%s', $ruleName, $value),
                $extendedError,
            );
        }

        $negative = match ($value[0]) {
            '+' => false,
            '-' => true,
            default => null,
        };
        $number = \substr($value, $negative !== null ? 1 : 0);
        if (! \is_numeric($number) || \intval($number) < 1 || \intval($number) > $max) {
            throw self::error(
                sprintf('%s=%s', $ruleName, $value),
                $extendedError,
            );
        }

        return $negative ? -\intval($number) : \intval($number);
    }

    public static function error(string $rule, string|null $extended = null): UnexpectedValueException
    {
        $extendedInfo = $extended ? sprintf(' because %s', $extended) : '';

        return new UnexpectedValueException(sprintf('Parse error near: %s', $rule) . $extendedInfo);
    }
}
