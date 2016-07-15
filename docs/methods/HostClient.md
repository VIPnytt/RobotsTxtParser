# Class HostClient
```php
@package vipnytt\RobotsTxtParser\Client\Directives
```

### Directives:
- [Host](../directives.md#host)

## Public functions
- [export](#export)
- [get](#get)
- [getWithFallback](#getwithfallback)
- [isPreferred](#ispreferred)
- [isUriListed](#isurilisted)

### export
```php
@return string[]|string|null
```
Export the host(s).

### get
```php
@return string|null
```
Get the Host listed by the directive.

### getWithFallback
```php
@return string
```
Get the Host listed by the directive. Falls back to the host of the "Effective Request URI" when no hosts are defined.

### isPreferred
```php
@return bool
```
Check if the requested host also is the preferred one.

### isUriListed
```php
@param string $uri
@return bool
```
Check if the host of the given URL is listed by any Host directive.
