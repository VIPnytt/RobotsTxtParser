[![Build Status](https://travis-ci.org/VIPnytt/RobotsTxtParser.svg?branch=master)](https://travis-ci.org/VIPnytt/RobotsTxtParser)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/VIPnytt/RobotsTxtParser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/VIPnytt/RobotsTxtParser/?branch=master)
[![Code Climate](https://codeclimate.com/github/VIPnytt/RobotsTxtParser/badges/gpa.svg)](https://codeclimate.com/github/VIPnytt/RobotsTxtParser)
[![Test Coverage](https://codeclimate.com/github/VIPnytt/RobotsTxtParser/badges/coverage.svg)](https://codeclimate.com/github/VIPnytt/RobotsTxtParser/coverage)
[![License](https://poser.pugx.org/VIPnytt/RobotsTxtParser/license)](https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE)
[![Packagist](https://img.shields.io/packagist/v/vipnytt/robotstxtparser.svg)](https://packagist.org/packages/vipnytt/robotstxtparser)
[![Chat](https://badges.gitter.im/VIPnytt/RobotsTxtParser.svg)](https://gitter.im/VIPnytt/RobotsTxtParser)

# Robots.txt parser
An easy to use, extensible PHP library to parse `robots.txt` according to [_Google_](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt), [_Yandex_](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml), [_W3C_](https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1) and [_The Web Robots Pages_](http://www.robotstxt.org/robotstxt.html) specifications.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/6fb47427-166b-45d0-bd41-40f7a63c2b0c/big.png)](https://insight.sensiolabs.com/projects/6fb47427-166b-45d0-bd41-40f7a63c2b0c)

#### Usage cases:
- Permission checks
- Fetch crawler rules
- Sitemap discovery
- Host preference
- Dynamic URL parameter discovery
- `robots.txt` rendering

### Optional features
- Automatic download
- [Caching system](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/sql/cache.md)
- [Delay handler](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/sql/delay.md)

#### Advantages
_(compared to most other robots.txt libraries)_
- Full support for [every single directive](#directives-supported), from [every specification](#specifications).
- HTTP Status code handler, _according to [Google](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt) spec._
- Dedicated `User-Agent` parser and group determiner library, for maximum accuracy.
- Full support for _inline directives_, _according to [Yandex](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml) spec._
- Provides additional data like _preferred host_, dynamic _URL parameters_, _Sitemap_ locations, etc.
- Supports ``HTTP``, ``HTTPS``, ``FTP``, ``SFTP`` and ``FTP/S``.

#### Requirements:
- PHP [>=5.6](http://php.net/supported-versions.php)
- PHP [cURL](http://php.net/manual/en/book.curl.php) extension
- PHP [iconv](http://php.net/manual/en/book.iconv.php) extension
- PHP [mbstring](http://php.net/manual/en/book.mbstring.php) extension
- PHP [OpenSSL](http://php.net/manual/en/book.openssl.php) extension

Note: HHVM support is planned once [facebook/hhvm#4277](https://github.com/facebook/hhvm/issues/4277) is fixed.

## Installation
The recommended way to install the robots.txt parser is through [Composer](http://getcomposer.org). Add this to your `composer.json` file:
```json
{
    "require": {
        "vipnytt/robotstxtparser": "~2.0"
    }
}
```
Then run: ```php composer.phar update```

## Getting started
### Basic usage example
```php
$client = new vipnytt\RobotsTxtParser\UriClient('http://example.com');

if ($client->userAgent('MyBot')->isAllowed('http://example.com/somepage.html')) {
    // Access is granted
}
if ($client->userAgent('MyBot')->isDisallowed('http://example.com/admin')) {
    // Access is denied
}
```
### Some more methods
```php
// Syntax: $baseUri, [$statusCode:int|null], [$robotsTxtContent:string], [$encoding:string], [$byteLimit:int|null]
$client = new vipnytt\RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);

// Permission checks
$allowed = $client->userAgent('MyBot')->isAllowed('http://example.com/somepage.html'); // bool
$denied = $client->userAgent('MyBot')->isDisallowed('http://example.com/admin'); // bool

// Crawl delay rules
$crawlDelay = $client->userAgent('MyBot')->crawlDelay()->get(); // float | int

// Dynamic URL parameters
$cleanParam = $client->cleanParam()->export(); // array
$cleanParam = $client->cleanParam()->isListed('param'); // bool

// Preferred host
$host = $client->host()->get(); // string | null
$host = $client->host()->isPreferred(); // bool

// XML Sitemap locations
$host = $client->sitemap()->export(); // array
```

Visit the [Documentation](https://github.com/VIPnytt/RobotsTxtParser/tree/master/docs) for even more methods, possibilities and additional usage examples.

## Specifications
- [x] [Google robots.txt specifications](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt)
- [x] [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml)
- [x] [W3C Recommendation HTML 4.01 specification](https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.2)
- [x] [Sitemaps.org protocol](http://www.sitemaps.org/protocol.html#submit_robots)
- [x] [Sean Conner: _"An Extended Standard for Robot Exclusion"_](http://www.conman.org/people/spc/robots2.html)
- [x] [Martijn Koster: _"A Method for Web Robots Control"_](http://www.robotstxt.org/norobots-rfc.txt)
- [x] [Martijn Koster: _"A Standard for Robot Exclusion"_](http://www.robotstxt.org/orig.html)

Note: _Where the specifications/drafts differ from each other, the most specific parts is implemented in this library._

### Directives
- [x] [`User-agent`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#user-agent)
  - [x] [`Allow`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#allow)
    - [x] _inline_ [`Clean-param`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#inline-clean-param)
    - [x] _inline_ [`Host`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#inline-host)
  - [x] [`Disallow`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#disallow)
    - [x] _inline_ [`Clean-param`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#inline-clean-param)
    - [x] _inline_ [`Host`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#inline-host)
  - [x] [`NoIndex`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#noindex)
    - [x] _inline_ [`Clean-param`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#inline-clean-param)
    - [x] _inline_ [`Host`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#inline-host)
  - [x] [`Crawl-delay`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#crawl-delay)
  - [x] [`Cache-delay`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#cache-delay)
  - [x] [`Request-rate`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#request-rate)
  - [x] [`Visit-time`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#visit-time)
  - [x] [`Robot-version`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#robot-version)
- [x] [`Clean-param`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#clean-param)
- [x] [`Host`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#host)
- [x] [`Sitemap`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/directives.md#sitemap)
