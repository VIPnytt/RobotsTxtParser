# Class Cache
```
@package vipnytt\RobotsTxtParser
```

## Public functions
- [__construct](#__construct)
- [clean](#clean)
- [client](#client)
- [cron](#cron)
- [invalidate](#invalidate)

### __construct
```
@param PDO $pdo
@param array $curlOptions
@param int|null $byteLimit
```

### clean
```
@param int $delay - in seconds
@return bool
```
Clean the cache for any inactive out-of-date records.

This _may_ save you for some disk space, but don't over-estimate it. Internal tests is showing that 10.000 cached `robots.txt` files, only takes up about 5-6 Megabytes in the database. Additionally, most deleted `robots.txt` files usually shows up in the databases again after a shorter or longer period of time, depending on how often your crawler requests access to these hosts.

### client
```
@param string $baseUri
@return TxtClient
```
Returns an instance of [TxtClient](TxtClient.md).

### cron
```
@param float|int $targetTime
@param int|null $workerID
@return string[]
```
This method is optional, but is highly recommended to implement!

Intended for periodic execution (like a _Cron job_). Updates the cache of outdated (but active) `robots.txt` records.

If an cached `robots.txt` record is kept updated by this cron job, both resources and request waiting times are freed from the [client](#client), allowing it do do it's job dramatically faster.

### invalidate
```
@param $baseUri
@return bool
```
Invalidates and deletes the cache for an specified host.
