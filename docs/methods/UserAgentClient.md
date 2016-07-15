# Class UserAgentClient
```php
@package vipnytt\RobotsTxtParser\Client\Directives
```

### Directives:
- [User-agent](../directives.md#user-agent)

## Public functions
- [allow](#allow)
- [cacheDelay](#cachedelay)
- [comment](#comment)
- [crawlDelay](#crawldelay)
- [disallow](#disallow)
- [export](#export)
- [isAllowed](#isallowed)
- [isDisallowed](#isdisallowed)
- [noIndex](#noindex)
- [requestRate](#requestrate)
- [robotVersion](#robotversion)
- [visitTime](#visittime)

### allow
```php
@return AllowClient
```
Wrapper for the [Allow](../directives.md#allow) directive.

Returns an instance of [AllowClient](AllowClient.md).

### cacheDelay
```php
@return DelayClient
```
Wrapper for the [Cache-delay](../directives.md#cache-delay) directive.

Returns an instance of [DelayClient](DelayClient.md).

### comment
```php
@return CommentClient
```
Wrapper for the [Comment](../directives.md#comment) directive.

Returns an instance of [CommentClient](CommentClient.md).

### crawlDelay
```php
@return DelayClient
```
Wrapper for the [Crawl-delay](../directives.md#crawl-delay) directive.

Returns an instance of [DelayClient](DelayClient.md).

### disallow
```php
@return AllowClient
```
Wrapper for the [Disallow](../directives.md#disallow) directive.

Returns an instance of [AllowClient](AllowClient.md).

### export
```php
@return array
```
Export an array of rules for the current User-agent.

### isAllowed
```php
@param string $uri
@return bool
```
Check if an URI is allowed to crawl.

### isDisallowed
```php
@param string $uri
@return bool
```
Check if an URI is disallowed to crawl.

### noIndex
```php
@return AllowClient
```
Wrapper for the [NoIndex](../directives.md#noindex) directive.

Returns an instance of [AllowClient](AllowClient.md).

### requestRate
```php
@return RequestRateClient
```
Wrapper for the [Request-rate](../directives.md#request-rate) directive.

Returns an instance of [RequestRateClient](RequestRateClient.md).

### robotVersion
```php
@return RobotVersionClient
```
Wrapper for the [Robot-version](../directives.md#robot-version) directive.

Returns an instance of [RobotVersionClient](RobotVersionClient.md).

### visitTime
```php
@return VisitTimeClient
```
Wrapper for the [Visit-time](../directives.md#visit-time) directive.

Returns an instance of [VisitTimeClient](VisitTimeClient.md).
