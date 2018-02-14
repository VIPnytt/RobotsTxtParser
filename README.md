[![Build Status](https://travis-ci.org/VIPnytt/RobotsTxtParser.svg?branch=master)](https://travis-ci.org/VIPnytt/RobotsTxtParser)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/VIPnytt/RobotsTxtParser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/VIPnytt/RobotsTxtParser/?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/f0eead8b4150095112da/maintainability)](https://codeclimate.com/github/VIPnytt/RobotsTxtParser/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/f0eead8b4150095112da/test_coverage)](https://codeclimate.com/github/VIPnytt/RobotsTxtParser/test_coverage)
[![License](https://poser.pugx.org/VIPnytt/RobotsTxtParser/license)](https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE)
[![Packagist](https://img.shields.io/packagist/v/vipnytt/robotstxtparser.svg)](https://packagist.org/packages/vipnytt/robotstxtparser)
[![Gitter](https://badges.gitter.im/VIPnytt/RobotsTxtParser.svg)](https://gitter.im/VIPnytt/RobotsTxtParser)

# Robots.txt parser
An easy to use, extensible `robots.txt` parser library with _full_ support for literally every [directive](#directives) and [specification](#specifications) _on the Internet_.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/6fb47427-166b-45d0-bd41-40f7a63c2b0c/big.png)](https://insight.sensiolabs.com/projects/6fb47427-166b-45d0-bd41-40f7a63c2b0c)

#### Usage cases:
- Permission checks
- Fetch crawler rules
- Sitemap discovery
- Host preference
- Dynamic URL parameter discovery
- `robots.txt` rendering

### Advantages
_(compared to most other robots.txt libraries)_
- Automatic `robots.txt` download. (optional)
- Integrated [Caching system](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/sql/cache.md). (optional)
- Crawl [Delay handler](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/sql/delay.md).
- [Documentation](https://github.com/VIPnytt/RobotsTxtParser/tree/master/docs) available.
- Support for literally [every single directive](#directives), from [every specification](#specifications).
- HTTP Status code handler, _according to [Google](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt)'s spec._
- Dedicated `User-Agent` parser and group determiner library, for maximum accuracy.
- Provides additional data like _preferred host_, dynamic _URL parameters_, _Sitemap_ locations, etc.
- Protocols supported: ``HTTP``, ``HTTPS``, ``FTP``, ``SFTP`` and ``FTP/S``.

#### Requirements:
- PHP 5.6 or 7.0+
- PHP extensions:
  - [cURL](http://php.net/manual/en/book.curl.php)
  - [mbstring](http://php.net/manual/en/book.mbstring.php)

## Installation
The recommended way to install the robots.txt parser is through [Composer](http://getcomposer.org). Add this to your `composer.json` file:
```json
{
  "require": {
    "vipnytt/robotstxtparser": "^2.0"
  }
}
```
Then run: ```php composer update```

## Getting started
### Basic usage example
```php
<?php
$client = new vipnytt\RobotsTxtParser\UriClient('http://example.com');

if ($client->userAgent('MyBot')->isAllowed('http://example.com/somepage.html')) {
    // Access is granted
}
if ($client->userAgent('MyBot')->isDisallowed('http://example.com/admin')) {
    // Access is denied
}
```
### A small excerpt of basic methods
```php
<?php
// Syntax: $baseUri, [$statusCode:int|null], [$robotsTxtContent:string], [$encoding:string], [$byteLimit:int|null]
$client = new vipnytt\RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);

// Permission checks
$allowed = $client->userAgent('MyBot')->isAllowed('http://example.com/somepage.html'); // bool
$denied = $client->userAgent('MyBot')->isDisallowed('http://example.com/admin'); // bool

// Crawl delay rules
$crawlDelay = $client->userAgent('MyBot')->crawlDelay()->getValue(); // float | int

// Dynamic URL parameters
$cleanParam = $client->cleanParam()->export(); // array

// Preferred host
$host = $client->host()->export(); // string | null
$host = $client->host()->getWithUriFallback(); // string
$host = $client->host()->isPreferred(); // bool

// XML Sitemap locations
$host = $client->sitemap()->export(); // array
```

The above is just a taste the basics, a whole bunch of more advanced and/or specialized methods are available for almost any purpose. Visit the [cheat-sheet](https://github.com/VIPnytt/RobotsTxtParser/tree/master/docs/CheatSheet.md) for the technical details.

Visit the [Documentation](https://github.com/VIPnytt/RobotsTxtParser/tree/master/docs) for more information.

### Directives
- [`Clean-param`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/Directives.md#clean-param)
- [`Host`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/Directives.md#host)
- [`Sitemap`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/Directives.md#sitemap)
- [`User-agent`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/Directives.md#user-agent)
  - [`Allow`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/Directives.md#allow)
  - [`Cache-delay`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/Directives.md#cache-delay)
  - [`Comment`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/Directives.md#comment)
  - [`Crawl-delay`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/Directives.md#crawl-delay)
  - [`Disallow`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/Directives.md#disallow)
  - [`NoIndex`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/Directives.md#noindex)
  - [`Request-rate`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/Directives.md#request-rate)
  - [`Robot-version`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/Directives.md#robot-version)
  - [`Visit-time`](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/Directives.md#visit-time)

## Specifications
- [Google robots.txt specifications](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt)
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml)
- [W3C Recommendation HTML 4.01 specification](https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1)
- [Sitemaps.org protocol](http://www.sitemaps.org/protocol.html#submit_robots)
- [Sean Conner: _"An Extended Standard for Robot Exclusion"_](http://www.conman.org/people/spc/robots2.html)
- [Martijn Koster: _"A Method for Web Robots Control"_](http://www.robotstxt.org/norobots-rfc.txt)
- [Martijn Koster: _"A Standard for Robot Exclusion"_](http://www.robotstxt.org/orig.html)
- [RFC 7231](https://tools.ietf.org/html/rfc7231), [~~2616~~](https://tools.ietf.org/html/rfc2616)
- [RFC 7230](https://tools.ietf.org/html/rfc7230), [~~2616~~](https://tools.ietf.org/html/rfc2616)
- [RFC 5322](https://tools.ietf.org/html/rfc5322), [~~2822~~](https://tools.ietf.org/html/rfc2822), [~~822~~](https://tools.ietf.org/html/rfc822)
- [RFC 3986](https://tools.ietf.org/html/rfc3986), [~~1808~~](https://tools.ietf.org/html/rfc3986)
- [RFC 1945](https://tools.ietf.org/html/rfc1945)
- [RFC 1738](https://tools.ietf.org/html/rfc1738)
- [RFC 952](https://tools.ietf.org/html/rfc952)
