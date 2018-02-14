# Getting started

- [What class to use?](#create-an-robotstxt-parser-instance)
- [UriClient](#alternative-1---automatic-download)
- [TxtClient](#alternative-2---custom-robotstxt-input)
- [Cache](#alternative-3---the-integrated-caching-system)
- [Import](#alternative-4---import-an-array-of-rules)

## Create an `robots.txt` parser instance
There's a couple of different ways to construct the `robots.txt` parser, each suited for different demands.

### Alternative 1 - Automatic download
```php
<?php
$client = new \vipnytt\RobotsTxtParser\UriClient('http://example.com');
```
- [Documentation + additional UriClient specific methods](methods/UriClient.md).
- [Usage examples](CheatSheet.md#uriclient)

### Alternative 2 - Custom `robots.txt` input
```php
<?php
$robotsTxt = <<<TXT
User-agent: *
Disallow: /
Allow: /public
Crawl-delay: 5
TXT;
$client = new \vipnytt\RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxt);
```
- [Documentation](methods/TxtClient.md).
- [Usage examples](CheatSheet.md#txtclient)

### Alternative 3 - The integrated caching system
```php
<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=database', 'username', 'password');
$handler = new \vipnytt\RobotsTxtParser\Database($pdo);
$client = $handler->cache()->base('http://example.com')->client();
```
- [Set-up instructions](sql/cache.md).
- [Documentation + special methods](methods/CacheManageInterface.md).
- [Usage examples](CheatSheet.md#cache-manage)

### Alternative 4 - Import an array of rules
Intended for special cases only. Import an array earlier exported by the [TxtClient](methods/TxtClient.md#export).
```php
<?php
$import = new \vipnytt\RobotsTxtParser\Import($array);
```
- [Documentation + special methods](methods/Import.md).
- [Usage examples](CheatSheet.md#import)
