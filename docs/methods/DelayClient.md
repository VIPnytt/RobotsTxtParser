# Class DelayClient
```
@package vipnytt\RobotsTxtParser\Client\Directives
```
### Directives:
- [Cache-delay](../directives.md#cache-delay)
- [Crawl-delay](../directives.md#crawl-delay)

## Public functions

### getValue
```
@return float|int
```
Get the numeric value of the directive.

__`Cache-delay` specific:__
When the value is requested but not found, the value of [``Crawl-delay``](../directives.md#crawl-delay) is returned, to maintain compatibility.

### export
```
@return float|int
```
Get the numeric value of the directive.

### handle
````
@param PDO $pdo
@return DelayHandlerClient
````
Returns an instance of the [DelayHandlerClient](DelayHandlerClient.md).

__`Cache-delay` specific:__
When the value is requested but not found, the value of [``Crawl-delay``](../directives.md#crawl-delay) is returned, to maintain compatibility.

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
