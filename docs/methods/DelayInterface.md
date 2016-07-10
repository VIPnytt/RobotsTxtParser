# Class DelayInterface
```
@package vipnytt\RobotsTxtParser\Client\Delay
```

## Public functions
- [export](#export)
- [getBaseUri](#getbaseuri)
- [getUserAgent](#getuseragent)
- [getValue](#getvalue)
- [handle](#handle)

### getQueue
```
@return float|int
```
Check how many seconds to wait if you'll apply to the queue now. This methods is intended for usage as an status check, and does NOT put you in the queue.

### getTimeSleepUntil
```
@return float|int
```
Get the timestamp (with milliseconds) wou'll have to wait until before you'll send the request.

### reset
```
@param float|int|null $delay
@return bool
```
Reset the queue for the current host. Optionally set an custom delay to reset to.

### sleep
```
@return float|int
```
Sleep until it's your turn to send the request.
