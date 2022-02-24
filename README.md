# Recurrence Rules

![Tests](https://github.com/brzuchal/recurrence-rules/actions/workflows/php.yml/badge.svg)

---

A recurrence processor for PHP

This library parses recurrence strings as defined in RFC 5545 and RFC 2445 and iterates the instances.
In addition, it can be used to build valid recurrence strings in a convenient manner.

The Rule class is implemented in immutable manner, use RuleBuilder for convenient approcha to build Rule objects.

Please note that the interface of the classes in this library is not finalized yet and subject to change.

## Install

```shell
composer require brzuchal/recurrence-rules
```

## Usage

```php
use Brzuchal\RecurrenceRule\RfcParser;

$rule = RfcParser::fromString('FREQ=MONTHLY;COUNT=1;INTERVAL=2;BYDAY=MO,TU;WKST=TU')
echo $rule->toString(); // FREQ=MONTHLY;COUNT=1;INTERVAL=2;BYDAY=MO,TU;WKST=TU
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
