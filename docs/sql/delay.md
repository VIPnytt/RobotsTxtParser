# Delay handler
Some hosts requires you to control the request flow, and not send the requests too frequent. The reasons for this isn't always obvious, and may sometimes be complicated.

Directives examples:
- [`Crawl-delay: 5 #seconds`](../directives.md#crawl-delay)
- [`Cache-delay: 10 #seconds`](../directives.md#cache-delay)
- [`Request-rate: 500/1h # 7.2 seconds (500 requests / 1 hour)`](../directives.md#request-rate)

#### Shared-setup compatible
Multiple user-agents / crawlers may share the same database/host. The delay is handled and stored individually for each User-agent, leaving no worries.

### Requirements
- MySQL 5.6+

Support for additional databases is possible, just [submit an issue](https://github.com/VIPnytt/RobotsTxtParser/issues) and we'll see what we can do about it.

## Usage
#### Automated
Sleep until the timestamp is reached
```php
if ($client->userAgent('MyBot')->isAllowed('http://example.com/path/to/file')) {
    // Crawl allowed
    $client->userAgent('MyBot')->crawlDelay()->sql($pdo)->sleep();
    // Put your crawling code here!
}
```
#### Semi-automated
Get timestamp with micro seconds
```php
if ($client->userAgent('MyBot')->isAllowed('http://example.com/path/to/file')) {
    // Crawl allowed
    $timestamp = $client->userAgent('MyBot')->crawlDelay()->sql($pdo)->getMicroTime();
    time_sleep_until($timestamp);
    // Put your crawling code here!
}
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

#### Security
For the sake of security, it is recommended to use a dedicated user with a bare minimum of permissions:

- `robotstxt__delay0`
  - `SELECT`
  - `INSERT`
  - `UPDATE`
  - `DELETE` - (maintenance only)
