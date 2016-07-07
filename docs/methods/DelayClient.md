# Class DelayClient
```
@package vipnytt\RobotsTxtParser\Client\Directives
```

### Directives:
- [Cache-delay](../directives.md#cache-delay)
- [Crawl-delay](../directives.md#crawl-delay)

## Public functions
- [export](#export)
- [getBaseUri](#getbaseuri)
- [getUserAgent](#getuseragent)
- [getValue](#getvalue)
- [handle](#handle)

### export
```
@return float|int
```
Get the numeric value of the directive.

### getBaseUri
```
@return string
```
Get the base URI, witch the directive applies to.

### getUserAgent
```
@return string
```
Get selected user-agent.

### getValue
```
@return float|int
```
Get the numeric value of the directive.

__`Cache-delay` specific:__
When the value is requested but not found, the value of [``Crawl-delay``](../directives.md#crawl-delay) is returned, to maintain compatibility.

### handle
````
@param PDO $pdo
@return DelayHandlerClient
````
Returns an instance of [DelayHandlerClient](DelayHandlerClient.md).

__`Cache-delay` specific:__
When the value is requested but not found, the value of [``Crawl-delay``](../directives.md#crawl-delay) is returned, to maintain compatibility.
