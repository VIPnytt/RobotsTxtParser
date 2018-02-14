# Interface Cache BaseInterface
```php
@package vipnytt\RobotsTxtParser\Client\Cache
```

Cache handler

## Public functions
- [client](#client)
- [debug](#debug)
- [invalidate](#invalidate)

### client
```php
@return Client\TxtClient
```
Returns an instance of [TxtClient](TxtClient.md).

### debug
```php
@param string $base
@return array
```
Get the RAW data from the database.

### invalidate
```php
@param $baseUri
@return bool
```
Invalidates and deletes the cache for an specified host.
