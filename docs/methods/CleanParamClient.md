# Class CleanParamClient
```php
@package vipnytt\RobotsTxtParser\Client\Directives
```

### Directives:
- [Clean-param](../Directives.md#clean-param)

## Public functions
- [parent::InlineCleanParamClient](#parentinlinecleanparamclient)
- [detectWithCommon](#detectwithcommon)

### parent::InlineCleanParamClient
The `CleanParamClient` class extends the [`InlineCleanParamClient`](InlineCleanParamClient.md) class, every single public function from that class class, is also available here.

See [`InlineCleanParamClient`](InlineCleanParamClient.md) for more information.

### detectWithCommon
```php
@param string $uri
@param string[] $customParam
@return string[]
```
Array of uri parameters witch are considered dynamic. This function includes generic dynamic parameters, in addition to those in the `robots.txt` and in the input parameter `$customParam` (optional).
