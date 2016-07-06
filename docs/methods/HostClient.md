# Class HostClient
```
@package vipnytt\RobotsTxtParser\Client\Directives
```
### Directives:
- [Host](../directives.md#host)

## Public functions

### isPreferred
```
@return bool
```
Check if the requested host also is the preferred one.

### get
```
@return string|null
```
Get the Host listed by the directive.

### getWithFallback
```
@return string
```
Get the Host listed by the directive. Falls back to the host of the effective URL (request URL after any redirects).

### isUriListed
```
@param string $uri
@return bool
```
Check if the host of the given URL is listed by any Host directive.

### export
```
@return string[]|string|null
```
Export the host(s).
