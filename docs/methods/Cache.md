# Class Cache
```
@package vipnytt\RobotsTxtParser
```

## Public functions
- [__construct](#__construct)
- [client](#client)
- [cron](#cron)
- [getTopWaitTimes](#gettopwaittimes)

### __construct
```
@param PDO $pdo
@param array $curlOptions
@param int|null $byteLimit
```

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

### getTopWaitTimes
```
@param int $limit
@param int $min
@return array
```
Get an array of the hosts with highest wait-time. Such hosts are either frequently requested, or has set an unusual high delay in their `robots.txt` file.
