# Delay handler
Many hosts requires you to control the robot's request flow, using a minimum interval between each request. The reasons for this isn't always obvious, and may sometimes be complicated.

Did you know this number is as high as about 40% of all `robots.txt` files?

Examples of a 5 second interval:
- [`Crawl-delay: 5`](../Directives.md#crawl-delay)
- [`Cache-delay: 5`](../Directives.md#cache-delay)
- [`Request-rate: 720/1h`](../Directives.md#request-rate)

#### Shared-setup compatible
Multiple _user-agents_ / _crawlers_ may share the same database. The delay is handled and stored individually for each user-agent, leaving no worries.

### Requirements
- MySQL 5.6+ _(or similar replacement, such as MariaDB)_

The library is developed with cross-system support in mind, and everything is set for additional database support. Just [submit an issue](https://github.com/VIPnytt/RobotsTxtParser/issues) and we'll see what we can do about it.

## Usage
- See the [Delay base interface](../methods/DelayBaseInterface.md) for client usage
- See the [Delay manage interface](../methods/DelayManageInterface.md) for management

#### Automated
Sleep until the timestamp is reached
```php
<?php
$db = new \vipnytt\RobotsTxtParser\Database($pdo);
if ($client->userAgent('MyBot')->isAllowed('http://example.com/path/to/file')) {
    // Crawl allowed
    $client->userAgent('MyBot')->crawlDelay()->handle($db->delay())->sleep();
    // Put your crawling code here!
}
```
#### Semi-automated
Get timestamp with micro seconds
```php
<?php
$db = new \vipnytt\RobotsTxtParser\Database($pdo);
if ($client->userAgent('MyBot')->isAllowed('http://example.com/path/to/file')) {
    // Crawl allowed
    $timestamp = $client->userAgent('MyBot')->crawlDelay()->handle($db->delay())->getTimeSleepUntil();
    time_sleep_until($timestamp);
    // Put your crawling code here!
}
```

#### Table maintenance
Clean old data:
```php
<?php
$db = new \vipnytt\RobotsTxtParser\Database($pdo);
$db->delay()->clean();
```

## Issues
In case of problems, please [submit an issue](https://github.com/VIPnytt/RobotsTxtParser/issues).

## Setup instructions
Run this `SQL` script:
```SQL
CREATE TABLE `robotstxt__delay0` (
  `base`       VARCHAR(269)
               COLLATE ascii_bin   NOT NULL,
  `userAgent`  VARCHAR(63)
               COLLATE ascii_bin   NOT NULL,
  `delayUntil` BIGINT(20) UNSIGNED NOT NULL,
  `lastDelay`  BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`base`, `userAgent`),
  KEY `delayUntil` (`delayUntil`)
)
  DEFAULT CHARSET = ascii
  COLLATE = ascii_bin
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
