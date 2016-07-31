# Class Cache
```php
@package vipnytt\RobotsTxtParser
```

## Public functions
- [__construct](#__construct)
- [clean](#clean)
- [client](#client)
- [cron](#cron)
- [debug](#debug)
- [invalidate](#invalidate)

### __construct
```php
@param PDO $pdo
@param array $curlOptions
@param int|null $byteLimit
```

### clean
```php
@param int $delay - in seconds
@return bool
```
Clean the cache for any inactive out-of-date records.

This _may_ save you for some disk space, but don't over-estimate it. Internal tests is showing that 10.000 cached `robots.txt` files, only takes up about 5 Megabytes in the database. Additionally, most deleted `robots.txt` files usually shows up in the databases again after a shorter or longer period of time, depending on how often your crawler requests access to these hosts.

### client
```php
@param string $baseUri
@return TxtClient
```
Returns an instance of [TxtClient](TxtClient.md).

### cron
```php
@param float|int $targetTime
@param int|null $workerID
@return string[]
```
This method is optional, but is highly recommended to implement!

Intended for periodic execution (like a _Cron job_). Updates the cache of outdated (but active) `robots.txt` records.

If an cached `robots.txt` record is kept updated by this cron job, both resources and request waiting times are freed from the [client](#client), allowing it do do it's job dramatically faster.

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
