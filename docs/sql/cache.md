# Robots.txt Cache
Caching the `robots.txt` files to the SQL server, greatly improves performance, avoids spamming of the remote host and any unnecessary network lag or timeouts is avoided.

It's common practice to cache the `robots.txt` for up to 24 hours.

#### Shared-setup compatible
Multiple crawlers may with benefits share the same database/host.

### Requirements:
- MySQL

Support for additional databases is possible, just [submit an issue](https://github.com/VIPnytt/RobotsTxtParser/issues) and we'll see what we can do about it.

## Usage
```php
$sql = new RobotsTxtParser\SQL($pdo);
$client = $sql->client('http://example.com');
```

#### Cron job
Recommended, but not required.

Automates the `robots.txt` cache update process, and makes sure the cache stays up to date. Faster client, less overhead.
```php
$sql = new RobotsTxtParser\SQL($pdo);
$cron = $sql->cron();
```

#### Table maintenance
Clean old data:
```php
$sql = new RobotsTxtParser\SQL($pdo);
$sql->maintenance()->cache()->clean();
```

## Issues
In case of problems, please [submit an issue](https://github.com/VIPnytt/RobotsTxtParser/issues).

## Setup instructions
All you need to do is create the SQL table in a database of your choice. You can do this two ways:

#### Create the table using PHP

```php
$sql = new RobotsTxtParser\SQL($pdo);
$sql->maintenance()->cache()->setup(); // bool
```

#### Create the table using an SQL script
```SQL
CREATE TABLE IF NOT EXISTS `robotstxt__cache0` (
  `base`       VARCHAR(250)
               COLLATE utf8_unicode_ci      NOT NULL,
  `content`    TEXT COLLATE utf8_unicode_ci NOT NULL,
  `statusCode` SMALLINT(4) UNSIGNED         NOT NULL,
  `validUntil` INT(10) UNSIGNED             NOT NULL,
  `nextUpdate` INT(10) UNSIGNED             NOT NULL,
  `worker`     TINYINT(3) UNSIGNED DEFAULT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

ALTER TABLE `robotstxt__cache0`
ADD PRIMARY KEY (`base`), ADD KEY `worker` (`worker`, `nextUpdate`);
```
Source: [/src/Client/SQL/Cache/cache.sql](https://github.com/VIPnytt/RobotsTxtParser/tree/master/src/Client/SQL/Cache/cache.sql)

#### Permissions
For the sake of security, it is recommended to use a dedicated user with a bare minimum of permissions.

__Permissions:__
- `SELECT`
- `INSERT`
- `UPDATE`
- `DELETE` - needed for maintenance only

__Tables:__
- `robotstxt__cache0`
