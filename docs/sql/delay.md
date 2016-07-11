# Delay handler
Many hosts requires you to control the robot's request flow, using a minimum interval between each request. The reasons for this isn't always obvious, and may sometimes be complicated.

Did you know this number is as high as about 40% of all `robots.txt` files?
_Source: internal statistics_

Examples of a 5 second interval:
- [`Crawl-delay: 5`](../directives.md#crawl-delay)
- [`Cache-delay: 5`](../directives.md#cache-delay)
- [`Request-rate: 720/1h`](../directives.md#request-rate)

#### Shared-setup compatible
Multiple _user-agents_ / _crawlers_ may share the same database. The delay is handled and stored individually for each user-agent, leaving no worries.

### Requirements
- MySQL 5.6+

The library is built with cross-system in mind, and everything is set for additional database support. Just [submit an issue](https://github.com/VIPnytt/RobotsTxtParser/issues) and we'll see what we can do about it.

## Usage
- See the [DelayInterface](../methods/DelayInterface.md) for client usage
- See the [Delay class](../methods/Delay.md) for management

#### Automated
Sleep until the timestamp is reached
```php
if ($client->userAgent('MyBot')->isAllowed('http://example.com/path/to/file')) {
    // Crawl allowed
    $client->userAgent('MyBot')->crawlDelay()->handle($pdo)->sleep();
    // Put your crawling code here!
}
```
#### Semi-automated
Get timestamp with micro seconds
```php
if ($client->userAgent('MyBot')->isAllowed('http://example.com/path/to/file')) {
    // Crawl allowed
    $timestamp = $client->userAgent('MyBot')->crawlDelay()->handle($pdo)->getTimeSleepUntil();
    time_sleep_until($timestamp);
    // Put your crawling code here!
}
```

#### Table maintenance
Clean old data:
```php
$handler = new RobotsTxtParser\Delay($pdo);
$handler->clean();
```

## Issues
In case of problems, please [submit an issue](https://github.com/VIPnytt/RobotsTxtParser/issues).

## Setup instructions
Run this `SQL` script:
```SQL
CREATE TABLE `robotstxt__delay0` (
  `base`       VARCHAR(269)
               CHARACTER SET ascii     NOT NULL,
  `userAgent`  VARCHAR(63)
               COLLATE utf8_unicode_ci NOT NULL,
  `delayUntil` BIGINT(20) UNSIGNED     NOT NULL,
  `lastDelay`  BIGINT(20) UNSIGNED     NOT NULL,
  PRIMARY KEY (`base`, `userAgent`),
  KEY `delayUntil` (`delayUntil`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci
```
Source: [/res/Delay/MySQL.sql](https://github.com/VIPnytt/RobotsTxtParser/blob/master/res/Delay/MySQL.sql)

#### Security
For the sake of security, it is recommended to use a dedicated user with a bare minimum of permissions:

- `robotstxt__delay0`
  - `SELECT`
  - `INSERT`
  - `UPDATE`
  - `DELETE`

#### Table version history
- `robotstxt__delay0` - [2.0 alpha](https://github.com/VIPnytt/RobotsTxtParser/releases/tag/v2.0.0-alpha.2) >>> [latest](https://github.com/VIPnytt/RobotsTxtParser/releases)
