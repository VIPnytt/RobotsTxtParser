# Class VisitTimeClient
```php
@package vipnytt\RobotsTxtParser\Client\Directives
```

### Directives:
- [Visit-time](../directives.md#visit-time)

## Public functions
- [export](#export)
- [isVisitTime](#isvisittime)

### export
```php
@return array
```
Export an array of visit-times in UTC.

### isVisitTime
```php
@param int|null $timestamp
@return bool
```
Check if the given time is an visit time. If no timestamp integer is provided, the current timestamp is used.
