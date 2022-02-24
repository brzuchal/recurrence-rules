# Recurrence Rules

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

$rule = RfcParser::fromString('FREQ=MONTHLY;COUNT=1;INTERVAL=2;BYDAY=MO,TU;BYMONTH=1,+2;BYSETPOS=1,2,4;WKST=TU')
echo $rule->toString(); // FREQ=MONTHLY;COUNT=1;INTERVAL=2;BYDAY=MO,TU;BYMONTH=1,+2;BYSETPOS=1,2,4;WKST=TU
```
