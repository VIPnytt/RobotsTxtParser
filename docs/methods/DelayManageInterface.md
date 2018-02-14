# Interface Delay ManageInterface
```php
@package vipnytt\RobotsTxtParser\Client\Delay
```

Delay manager

## Public functions
- [__construct](#__construct)
- [base](#base)
- [clean](#clean)
- [getTopWaitTimes](#gettopwaittimes)

### __construct
```php
@param PDO $pdo
```

### base
```php
@param string $baseUri
@param string $userAgent
@param float|int $delay
@return Client\Delay\BaseInterface
```
Returns an instance of [DelayInterface](DelayClient.md).

### clean
```php
@return bool
```
Cleans the database for any out-of-date delay-until records.

### getTopWaitTimes
```php
@param int $limit
@param int $min
@return array
```
Get an array of the hosts with highest wait-time. Such hosts are either frequently requested, or are demanding an unusual high delay time in their `robots.txt` file.
