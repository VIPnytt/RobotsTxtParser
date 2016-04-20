[![Build Status](https://travis-ci.org/VIPnytt/RobotsTxtParser.svg?branch=master)](https://travis-ci.org/VIPnytt/RobotsTxtParser)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/VIPnytt/RobotsTxtParser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/VIPnytt/RobotsTxtParser/?branch=master)
[![Code Climate](https://codeclimate.com/github/VIPnytt/RobotsTxtParser/badges/gpa.svg)](https://codeclimate.com/github/VIPnytt/RobotsTxtParser)
[![Test Coverage](https://codeclimate.com/github/VIPnytt/RobotsTxtParser/badges/coverage.svg)](https://codeclimate.com/github/VIPnytt/RobotsTxtParser/coverage)
[![License](https://poser.pugx.org/VIPnytt/RobotsTxtParser/license)](https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE)
[![Packagist](https://img.shields.io/packagist/v/vipnytt/robotstxtparser.svg)](https://packagist.org/packages/vipnytt/robotstxtparser)
[![Chat](https://badges.gitter.im/VIPnytt/RobotsTxtParser.svg)](https://gitter.im/VIPnytt/RobotsTxtParser)

# Robots.txt parser class
An easy to use, extensible PHP library to parse `robots.txt` according to [_Google_](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt), [_Yandex_](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml), [_W3C_](https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1) and [_The Web Robots Pages_](http://www.robotstxt.org/robotstxt.html) specifications.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/6fb47427-166b-45d0-bd41-40f7a63c2b0c/big.png)](https://insight.sensiolabs.com/projects/6fb47427-166b-45d0-bd41-40f7a63c2b0c)

#### Usage cases:
- Permission checks
- XML Sitemap detection
- Host preference detection
- Dynamic URL parameter detection

#### Advantages _(compared to most other robots.txt parsers)_
- Features a dedicated `User-Agent` parser and group determiner library, for maximum accuracy.
- HTTP Status code handler, _according to [Google](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt) spec._
- Full support for _inline directives_, _according to [Yandex](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml) spec._
- Provides additional data like _preferred host_, dynamic _URL parameters_ and _Sitemap_ locations.
- FTP support

#### Requirements:
- PHP [>=5.6](http://php.net/supported-versions.php)
- PHP [mbstring](http://php.net/manual/en/book.mbstring.php) extension

HHVM support is planned once [facebook/hhvm#4277](https://github.com/facebook/hhvm/issues/4277) is fixed.

## Installation
The recommended way to install the robots.txt parser is through [Composer](http://getcomposer.org). Add this to your `composer.json` file:
```json
{
    "require": {
        "vipnytt/robotstxtparser": "0.1.*"
    }
}
```
Then run
 ```bash
php composer.phar update
 ```

## Getting started
### Basic usage example
```php
$client = new vipnytt\RobotsTxtParser\Parser('http://example.com');
// When the robots.txt content isn't provided, it will be downloaded automatically
if ($client->userAgent('MyBot')->isAllowed('/somepage.html')) {
    // Access is granted
}
if ($client->userAgent('MyBot')->isDisallowed('/admin')) {
    // Access is denied
}
```
### Methods
```php
// Syntax: $baseUrl, [$statusCode=null], [$robotsTxtContent=null], [$encoding='UTF-8'], [$byteLimit=50000]
$client = new vipnytt\RobotsTxtParser\Client('http://example.com', 200, $robotsTxtContent);

// Permission checks
$allowed = $client->userAgent('MyBot')->isAllowed('/somepage.html'); // bool
$denied = $client->userAgent('MyBot')->isDisallowed('/admin'); // bool
// Crawl delay rules
$crawlDelay = $client->userAgent('MyBot')->getCrawlDelay(); // integer | float
$cacheDelay = $client->userAgent('MyBot')->getCacheDelay(); // integer | float
// Dynamic URL parameters
$cleanParam = $client->getCleanParam(); // array
// Preferred host
$host = $client->getHost(); // string | null
// XML Sitemap locations
$host = $client->getSitemaps(); // array
// Export
$exportAll = $client->export(); // array
$exportUA = $client->userAgent('MyBot')->export(); // array
```

See our [Wiki](https://github.com/VIPnytt/RobotsTxtParser/wiki) for [Documentation](https://github.com/VIPnytt/RobotsTxtParser/wiki) and additional [Usage examples](https://github.com/VIPnytt/RobotsTxtParser/wiki).

## Specifications
- [x] [Google's robots.txt specifications](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt)
- [x] [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml)
- [x] [The Web Robots Pages](http://www.robotstxt.org/)
- [x] [W3C Recommendation](https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.2)

### Directives supported
- [x] `User-Agent` group(s)
  - [x] `Allow` directive
    - [x] inline `Clean-param` directive
    - [x] inline `Host` directive
  - [x] `Disallow` directive
    - [x] inline `Clean-param` directive
    - [x] inline `Host` directive
  - [x] `Crawl-delay` directive
  - [x] `Cache-delay` directive
- [x] `Clean-param` directive
- [x] `Host` directive
- [x] `Sitemap` directive
