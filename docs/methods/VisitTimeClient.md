# Class VisitTimeClient
```
@package vipnytt\RobotsTxtParser\Client\Directives
```

### Directives:
- [Visit-time](../directives.md#visit-time)

## Public functions
- [export](#export)
- [isVisitTime](#isvisittime)

### export
```
@return array
```
Export an array of visit-times in UTC.

### isVisitTime
```
@param int|null $timestamp
@return bool
```
Check if the given time is an visit time. If no timestamp integer is provided, the current timestamp is used.
