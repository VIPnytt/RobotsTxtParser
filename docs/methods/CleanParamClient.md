# Class CleanParamClient
```php
@package vipnytt\RobotsTxtParser\Client\Directives
```

### Directives:
- [Clean-param](../Directives.md#clean-param)

## Public functions
- [detect](#detect)
- [detectWithCommon](#detectwithcommon)
- [export](#export)

### detect
```php
@param  string $uri
@return string[]
```
Detect and lists dynamic uri parameters.

### detectWithCommon
```php
@param string $uri
@param string[] $customParam
@return string[]
```
Array of uri parameters witch are considered dynamic. This function includes generic dynamic parameters, in addition to those in the `robots.txt` and in the input parameter `$customParam` (optional).


### export
```php
@return array
```
Export an array of parameters and their corresponding paths.
