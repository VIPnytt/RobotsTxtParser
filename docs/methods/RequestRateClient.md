# Class RequestRateClient
```php
@package vipnytt\RobotsTxtParser\Client\Directives
```

### Directives:
- [Request-rate](../Directives.md#request-rate)

## Public functions
- [export](#export)
- [getBaseUri](#getbaseuri)
- [getUserAgent](#getuseragent)
- [getValue](#getvalue)
- [handle](#handle)

### export
```php
@return array
```
All request-rates exported with their corresponding timestamps.

### getBaseUri
```php
@return string
```
Get the base URI, witch the directive applies to.

### getUserAgent
```php
@return string
```
Get selected user-agent.

### getValue
```php
@param int|null $timestamp
@return float|int
```
Get the rate for an given timestamp. If no timestamp integer is provided, current timestamp is used.

When the value is requested but not found, the value of [``Crawl-delay``](../Directives.md#crawl-delay) is returned, to maintain compatibility.

### handle
```php
@param Client\Delay\ManageInterface $handler
@return Client\Delay\BaseInterface
```
Returns an instance of [Client\Delay\ClientInterface](DelayBaseInterface.md).

When the value is requested but not found, the value of [``Crawl-delay``](../Directives.md#crawl-delay) is returned, to maintain compatibility.
