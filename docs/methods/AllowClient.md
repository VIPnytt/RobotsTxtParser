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

### export
```php
@return array
```
Export an array of the current directives rules.

### hasPath
```php
@param  string $uri
@return int|false
```
Check if the URI's path is covered by this directive. Returns rule string length on success, otherwise false.
