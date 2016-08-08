# Getting started

- [What class to use?](#create-an-robotstxt-parser-instance)
- [UriClient](#alternative-1---automatic-download)
- [TxtClient](#alternative-2---custom-robotstxt-input)
- [Cache](#alternative-3---the-integrated-caching-system)
- [Import](#import-an-array)
- [Delay](#the-delay-handler)

## Create an `robots.txt` parser instance
You have 3 different ways to construct the `robots.txt` parser, each suited for different demands.

### Alternative 1 - Automatic download
```php
$client = new \vipnytt\RobotsTxtParser\UriClient('http://example.com');
```
- [Documentation + additional UriClient specific methods](methods/UriClient.md).
- [Usage examples](CheatSheet.md#uriclient)

### Alternative 2 - Custom `robots.txt` input
```php
$robotsTxt = "
User-agent: *
Disallow: /
Allow: /public
Crawl-delay: 5
";
$client = new \vipnytt\RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxt);
```
- [Documentation](methods/TxtClient.md).
- [Usage examples](CheatSheet.md#txtclient)

### Alternative 3 - The integrated caching system
```php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=database', 'username', 'password');
$handler = new \vipnytt\RobotsTxtParser\Cache($pdo);
$client = $handler->client('http://example.com');
```
- [Set-up instructions](sql/cache.md).
- [Documentation + special methods](methods/Cache.md).
- [Usage examples](CheatSheet.md#cache)

## Import an array
Import an array already exported by the [TxtClient](methods/TxtClient.md#export).
```php
$import = new \vipnytt\RobotsTxtParser\Import($array);
```
- [Documentation + special methods](methods/Import.md).
- [Usage examples](CheatSheet.md#import)

## The Delay handler
The Delay class is mainly for administration purposes, but may also be used as an alternative way to handle delays. It is generally not needed, but available usage examples are shown below.
```php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=database', 'username', 'password');
$delayHandler = new \vipnytt\RobotsTxtParser\Delay($pdo);
```
- [Set-up instructions](sql/delay.md).
- [Documentation + special methods](methods/Delay.md).
- [Usage examples](CheatSheet.md#delay)

