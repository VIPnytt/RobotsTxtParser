# Class Delay
```php
@package vipnytt\RobotsTxtParser
```

## Public functions
- [__construct](#__construct)
- [clean](#clean)
- [client](#client)
- [getTopWaitTimes](#gettopwaittimes)

### __construct
```php
@param PDO $pdo
```

### clean
```php
@param int $delay - in seconds
@return bool
```
Cleans the database for any out-of-date delay-until records.

### client
```php
@param DelayInterface $client
@return ClientInterface
```
Returns an instance of [DelayInterface](DelayClient.md).

### getTopWaitTimes
```php
@param int $limit
@param int $min
@return array
```
Get an array of the hosts with highest wait-time. Such hosts are either frequently requested, or has set an unusual high delay in their `robots.txt` file.
