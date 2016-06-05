# Delay handler
Some hosts requires you to control the request flow, and not send the requests too frequent.

These directives are used to describe witch request delay to apply:
- [`Crawl-delay`](../directives.md#crawl-delay)
- [`Cache-delay`](../directives.md#cache-delay)
- [`Request-rate`](../directives.md#request-rate)

#### Shared-setup compatible
__Tip:__ Multiple user-agents / crawlers may share the same database/host. The delay is handled and stored individually for each User-agent, leaving no worries.

### Requirements
- MySQL 5.6+

Support for additional databases is possible, just [submit an issue](https://github.com/VIPnytt/RobotsTxtParser/issues) and we'll see what we can do about it.

## Usage
Sleep until the timestamp is reached
```php
$userAgent->crawlDelay()->sql($pdo)->sleep();
// Put your crawling code here!
```

Get the timestamp with micro seconds
```php
$timestamp = $userAgent->crawlDelay()->sql($pdo)->getMicroTime();
time_sleep_until($timestamp);
// Put your crawling code here!
```

#### Table maintenance
Clean old data:
```php
$sql = new RobotsTxtParser\SQL($pdo);
$sql->maintenance()->delay()->clean();
```

## Issues
In case of problems, please [submit an issue](https://github.com/VIPnytt/RobotsTxtParser/issues).

## Setup instructions
All you need to do is create the SQL table in a database of your choice. You can do this two ways:

#### Create the table using PHP

```php
$sql = new RobotsTxtParser\SQL($pdo);
$sql->maintenance()->delay()->setup(); // bool
```

#### Create the table using an SQL script
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
Source: [/src/Client/SQL/Delay/delay.sql](https://github.com/VIPnytt/RobotsTxtParser/tree/master/src/Client/SQL/Delay/delay.sql)

#### Permissions
For the sake of security, it is recommended to use a dedicated user with a bare minimum of permissions.

__Permissions:__
- `SELECT`
- `INSERT`
- `UPDATE`
- `DELETE` - needed for maintenance only

__Tables:__
- `robotstxt__delay0`
