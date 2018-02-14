# Class DelayClient
```php
@package vipnytt\RobotsTxtParser\Client\Directives
```

### Directives:
- [Cache-delay](../Directives.md#cache-delay)
- [Crawl-delay](../Directives.md#crawl-delay)

## Public functions
- [export](#export)
- [getBaseUri](#getbaseuri)
- [getUserAgent](#getuseragent)
- [getValue](#getvalue)
- [handle](#handle)

### export
```php
@return float|int
```
Get the numeric value of the directive.

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
@return float|int
```
Get the numeric value of the directive.

__`Cache-delay` specific:__
When the value is requested but not found, the value of [``Crawl-delay``](../Directives.md#crawl-delay) is returned, to maintain compatibility.

### handle
```php
@param Client\Delay\ManageInterface $handler
@return Client\Delay\BaseInterface
```
Returns an instance of [Client\Delay\ClientInterface](DelayBaseInterface.md).
