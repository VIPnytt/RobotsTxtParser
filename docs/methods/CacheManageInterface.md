# Interface Cache ManageInterface
```php
@package vipnytt\RobotsTxtParser\Client\Cache
```

Cache manager

## Public functions
- [__construct](#__construct)
- [base](#base)
- [clean](#clean)
- [cron](#cron)
- [setByteLimit](#setbytelimit)
- [setCurlOptions](#setcurloptions)

### __construct
```php
@param PDO $pdo
```

### base
```php
@param string $baseUri
@return Client\Cache\BaseInterface
```
Returns an instance of [BaseInterface](CacheBaseInterface.md).

### clean
```php
@param int $delay - in seconds
@return bool
```
Clean the cache for any inactive out-of-date records.

This _may_ save you for some disk space, but don't over-estimate it. Expect around 5 megabytes per 10.000 cached `robots.txt` files. Additionally, most deleted `robots.txt` files usually shows up in the databases again after a shorter or longer period of time, depending on how often your crawler requests access to these hosts.

### cron
```php
@param float|int $timeLimit
@param int|null $workerID
@return string[]
```
This method is optional, but is highly recommended to implement.

Intended for periodic execution (like a _Cron job_). Updates the cache of outdated (but active) `robots.txt` records.

If an cached `robots.txt` record is kept updated by this cron job, both resources and request waiting times are freed from the [base](#base), allowing it do do it's job significantly quicker.

### setByteLimit
```php
@param int|null $bytes
@return bool
```
Limit the `robots.txt` maximum file size witch is going to be parsed.

### setCurlOptions
```php
@param array $options
@return bool
```
Set an array of custom cURL options.
