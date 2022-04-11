# Recurrence Rules

![Tests](https://github.com/brzuchal/recurrence-rules/actions/workflows/continous-integration.yml/badge.svg)

---

## A message to Russian 🇷🇺 people

If you currently live in Russia, please read [this message](./ToRussianPeople.md).

## Purpose

A recurrence processor for PHP

This library parses recurrence strings as defined in RFC 5545 and RFC 2445 and iterates the instances.
In addition, it can be used to build valid recurrence strings in a convenient manner.

The Rule class is implemented in immutable manner, use RuleBuilder for convenient approch to build Rule objects.

> **NOTE!** Temporarily the RuleIterator relies on [rlanvin/php-rrule](https://github.com/rlanvin/php-rrule)
> wrapping the library under the hood while returning `DateTimeImmutable` objects instead.
> This approach is a temporary for complex logic of limiting and expanding which in future will be replaced
> with a set of filters/expanders in more OOP fashion way highly inspired by Java implementation of 
> [dmfs/lib-recur](https://github.com/dmfs/lib-recur/tree/master/src/main/java/org/dmfs/rfc5545/recur).

> **NOTE!** The interface of the classes in this library is not finalized yet and subject to change.

[![SWUbanner](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)

## Install

```shell
composer require brzuchal/recurrence-rules
```

## Usage

```php
use Brzuchal\RecurrenceRule\RuleFactory;
use Brzuchal\RecurrenceRule\RuleIterator;

$rule = RuleFactory::fromString('FREQ=MONTHLY;COUNT=4;INTERVAL=2;BYDAY=MO,TU;WKST=TU');
echo $rule->toString(); // FREQ=MONTHLY;COUNT=1;INTERVAL=2;BYDAY=MO,TU;WKST=TU

foreach (new RuleIterator(new DateTimeImmutable('2006-08-01'), $rule) as $occurrence) {
    echo $occurrence->format('Y-m-d'),", ";
} // 2006-08-01, 2006-08-07, 2006-08-08, 2006-08-14,
```

---

## License

MIT License

Copyright (c) 2022 Michał Marcin Brzuchalski

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
