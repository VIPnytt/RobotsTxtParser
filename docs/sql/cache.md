# Robots.txt Cache
Caching the `robots.txt` files to the SQL server, greatly improves performance, avoids spamming of the remote host and any unnecessary network lag or timeouts is avoided.

### Requirements:
- MySQL

Support for additional databases is possible, just [submit an issue](https://github.com/VIPnytt/RobotsTxtParser/issues) and we'll see what we can do about it.

### Usage
_Coming soon..._

In case of problems, please [submit an issue](https://github.com/VIPnytt/RobotsTxtParser/issues).

### Setup instructions
All you need to do is create the SQL table in a database of your choice. You can do this two ways:

__Option A: Create the table using PHP:__

```php
/**
 * Coming soon...
 */
```

__Option B: Create the table using this .sql script:__
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
