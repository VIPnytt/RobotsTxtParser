# Class HostClient
```php
@package vipnytt\RobotsTxtParser\Client\Directives
```

### Directives:
- [Host](../Directives.md#host)

## Public functions
- [export](#export)
- [getWithFallback](#getwithfallback)
- [isPreferred](#ispreferred)

### export
```php
@return string|null
```
Get the Host listed by the directive.

### getWithUriFallback
```php
@return string
```
Get the Host listed by the directive. Falls back to the host of the "Effective Request URI" if no host are defined.

### isPreferred
```php
@return bool
```
Check if the requested host also is the preferred one.
