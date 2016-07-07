# Class HostClient
```
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
```
@return string[]|string|null
```
Export the host(s).

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

### isPreferred
```
@return bool
```
Check if the requested host also is the preferred one.

### isUriListed
```
@param string $uri
@return bool
```
Check if the host of the given URL is listed by any Host directive.
