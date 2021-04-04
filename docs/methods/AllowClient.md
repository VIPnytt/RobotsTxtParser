# Class AllowClient
```php
@package vipnytt\RobotsTxtParser\Client\Directives
```

### Directives:
- [Allow](../Directives.md#allow)
- [Disallow](../Directives.md#disallow)
- [NoIndex](../Directives.md#noindex)

## Public functions
- [export](#export)
- [hasPath](#haspath)
- [isCovered](#iscovered)

### export
```php
@return array
```
Export an array of the current directives rules.

### hasPath
```php
@deprecated 2.1.0
@see AllowClient::isCovered()

@param  string $uri
@return int|false
```
**Note:** Deprecated as of version `2.1.0`. See [`isCovered()`](#iscovered) for replacement.

Check if the URI's path is covered by this directive. Returns rule string length on success, otherwise false.

### isCovered
```php
@since 2.1.0

@param  string $uri
@return string|false
```
**Note:** Available since version `2.1.0`.

Check if the URI's path is covered by this directive. Returns the most specific rule string on success, otherwise false.
