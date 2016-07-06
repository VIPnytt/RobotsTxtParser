# Class RequestRateClient
```
@package vipnytt\RobotsTxtParser\Client\Directives
```
### Directives:
- [Request-rate](../directives.md#request-rate)

## Public functions

### getValue
```
@param int|null $timestamp
@return float|int
```
Get the rate for an given timestamp. If no timestamp integer is provided, current timestamp is used.

When the value is requested but not found, the value of [``Crawl-delay``](../directives.md#crawl-delay) is returned, to maintain compatibility.

### export
```
@return array
```
All request-rates exported with their corresponding timestamps.

### handle
````
@param PDO $pdo
@return DelayHandlerClient
````
Returns an instance of the [DelayHandlerClient](DelayHandlerClient.md).

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
