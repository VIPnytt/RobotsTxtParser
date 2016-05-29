# Delay handler
Some hosts requires you to control the request flow, and not send the requests too frequent.

These directives are used to describe witch request delay to apply:
- [`Crawl-delay`](../directives.md#crawl-value)
- [`Cache-delay`](../directives.md#cache-value)
- [`Request-rate`](../directives.md#request-rate)

### Requirements
- MySQL 5.6+

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
CREATE TABLE IF NOT EXISTS `robotstxt__delay0` (
  `base`      VARCHAR(250)
              COLLATE utf8_unicode_ci NOT NULL,
  `userAgent` VARCHAR(250)
              COLLATE utf8_unicode_ci NOT NULL,
  `microTime` BIGINT(20) UNSIGNED     NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

ALTER TABLE `robotstxt__delay0`
ADD PRIMARY KEY (`base`, `userAgent`);
```
Source: [/src/RobotsTxtParser/Client/SQL/Delay/delay.sql](https://github.com/VIPnytt/RobotsTxtParser/tree/master/src/RobotsTxtParser/Client/SQL/Delay/delay.sql)
