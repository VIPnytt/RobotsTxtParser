# Robots.txt Cache
By caching data to the SQL server, overall performance is increased, you'll limit the number of network lags and timeouts to a bare minimum, and last but not least, no more spamming of the remote host.

It's common practice to cache the `robots.txt` for up to 24 hours.

#### Shared-setup compatible
Multiple crawlers may with benefits share the same database.

### Requirements:
- MySQL

Support for additional databases is possible, just [submit an issue](https://github.com/VIPnytt/RobotsTxtParser/issues) and we'll see what we can do about it.

## Usage
```php
$cache = new RobotsTxtParser\Cache($pdo);
$client = $cache->client('http://example.com');
```

#### Cron job
Recommended, but not required.

Automates the `robots.txt` cache update process, and makes sure the cache stays up to date. Faster client, less overhead.
```php
$cache = new RobotsTxtParser\Cache($pdo);
$cron = $cache->cron();
```

#### Table maintenance
Clean old data:
```php
$cache = new RobotsTxtParser\Cache($pdo);
$cache->clean();
```

## Issues
In case of problems, please [submit an issue](https://github.com/VIPnytt/RobotsTxtParser/issues).

## Setup instructions
Run this `SQL` script:
```SQL
CREATE TABLE `robotstxt__cache0` (
  `base`       VARCHAR(250)
               COLLATE utf8_unicode_ci      NOT NULL,
  `content`    TEXT COLLATE utf8_unicode_ci NOT NULL,
  `statusCode` SMALLINT(4) UNSIGNED         NOT NULL,
  `validUntil` INT(10) UNSIGNED             NOT NULL,
  `nextUpdate` INT(10) UNSIGNED             NOT NULL,
  `worker`     TINYINT(3) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`base`),
  KEY `worker` (`worker`, `nextUpdate`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci
```
Source: [/src/SQL/cache.sql](https://github.com/VIPnytt/RobotsTxtParser/tree/master/src/SQL/cache.sql)

#### Security
For the sake of security, it is recommended to use a dedicated user with a bare minimum of permissions:

- `robotstxt__cache0`
  - `SELECT`
  - `INSERT`
  - `UPDATE`
  - `DELETE`
