# Delay ClientInterface
```php
@package vipnytt\RobotsTxtParser\Client\Delay
```

## Public functions
- [getQueue](#getqueue)
- [getTimeSleepUntil](#gettimesleepuntil)
- [reset](#reset)
- [sleep](#sleep)

### getQueue
```php
@return float|int
```
Check how many seconds to wait if you'll apply to the queue now. This methods is intended for usage as an status check, and does NOT put you in the queue.

### getTimeSleepUntil
```php
@return float|int
```
Get the timestamp (with milliseconds) wou'll have to wait until before you'll send the request.

### reset
```php
@param float|int|null $delay
@return bool
```
Reset the queue for the current host. Optionally set an custom delay to reset to.

### sleep
```php
@return float|int
```
Sleep until it's your turn to send the request.
