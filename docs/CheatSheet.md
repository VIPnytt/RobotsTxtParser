# Cheat sheet
- [Cache manage](#cache-manage)
  - [Cache base](#cache-base)
- [Delay manage](#delay-manage)
  - [Delay base](#delay-base)
- [Import](#import)
- [UriClient](#uriclient)
- [TxtClient](#txtclient)

## Cache manage
[Cache documentation](methods/CacheManageInterface.md)

Note: _Most parameters available is set to their default values and is not shown. Referer to the documentation for the specific class, for a full overview._

__Example usage:__
```php
<?php
$db = new \vipnytt\RobotsTxtParser\Database($pdo);
$cacheManage = $db->cache();
```
Clean the cache for unused robots.txt files
```php
$cacheManage->clean();
```
Update the cache for any active robots.txt files
```php
$cacheManage->cron();
```
Set an upper limit of bytes to parse
```php
$cacheManage->setByteLimit($bytes);
```
Set an array of custom cURL options
```php
$cacheManage->setCurlOptions($array);
```
#### Create an [Cache base](#cache-base)
[Documentation](methods/TxtClient.md)
```php
$cacheManage->base('https://example.com');
```

### Cache base
[Documentation](methods/CacheManageInterface.md)

Note: _Most parameters available is set to their default values and is not shown. Referer to the documentation for the specific class, for a full overview._

__Example usage:__
```php
<?php
$db = new \vipnytt\RobotsTxtParser\Database($pdo);
$cacheBase = $db->cache()->base('https://example.com');
```
Get the RAW data from the database.
```php
$cacheBase->debug();
```
Invalidate the cache for an specific URI
```php
$cacheBase->invalidate();
```
#### Create an [TxtClient](#txtclient)
[Documentation](methods/TxtClient.md)

Create the TxtClient for parsing purposes
```php
$client = $handler->client('http://example.com');
```

## Delay manage
[Documentation](methods/DelayManageInterface.md)

Note: _Most parameters available is set to their default values and is not shown. Referer to the documentation for the specific class, for a full overview._

__Example usage:__
```php
<?php
$db = new \vipnytt\RobotsTxtParser\Database($pdo);
$delayManage = $db->delay();
```
Clean the delay storage for any outdated records
```php
$delayManage->clean();
```
Get an list of the hosts with highest wait-time.
```php
$delayManage->debug('http://example.com');
```
Get the RAW data from the database.
```php
$delayManage->getTopWaitTimes();
```

### Delay base
[Documentation](methods/DelayBaseInterface.md)

__Example usage:__
```php
<?php
$txtClient = new \vipnytt\RobotsTxtParser\TxtClient('http://example.com', 200, 'robots.txt');

$delayInterface = $txtClient->userAgent('myBot')->crawlDelay();
// or
$delayInterface = $txtClient->userAgent('myBot')->cacheDelay();
// or
$delayInterface = $txtClient->userAgent('myBot')->requestRate();

$db = new \vipnytt\RobotsTxtParser\Database($pdo);
$delayBase = $delayInterface->handle($db->delay());
```
Check the current request queue, returns the number of seconds of expected delay/sleep time.
```php
$delayBase->checkQueue();
```
Get the timestamp w/microseconds you'll have to wait until before sending the request
```php
$delayBase->getTimeSleepUntil();
```
Reset the global queue for this host
```php
$delayBase->reset();
```
Sleep until it's your turn to send the request
```php
$delayBase->sleep();
```

## Import
[Documentation](methods/Import.md)

__Example usage:__
```php
<?php
$client = new \vipnytt\RobotsTxtParser\Import($array);
```
Get the difference between the imported and the generated export array. Intended for debugging purposes only.
```php
$client->getIgnoredImportData();
```

The `Import` class extends the `TxtClient`. See [TxtClient](#txtclient) for the rest of the available methods.

## UriClient
[Documentation](methods/UriClient.md)

Note: _Most parameters available is set to their default values and is not shown. Referer to the documentation for the specific class, for a full overview._

__Example usage:__
```php
<?php
$client = new \vipnytt\RobotsTxtParser\UriClient('http://example.com');
```
Get base-URI
```php
$client->getBaseUri();
```
Get the robots.txt contents
```php
$client->getContents();
```
Get the effective base-URI (after any redirects)
```php
$client->getEffectiveUri();
```
Get the character encoding
```php
$client->getEncoding();
```
Get the HTTP/FTP status code
```php
$client->getStatusCode();
```
Next-update timestamp
```php
$client->nextUpdate();
```
The timestamp the robots.txt is valid until
```php
$client->validUntil();
```

The `UriClient` extends the `TxtClient`. See [TxtClient](#txtclient) for the rest of the available methods.

## TxtClient
[Documentation](methods/TxtClient.md)

Note: _Most parameters available is set to their default values and is not shown. Referer to the documentation for the specific class, for a full overview._

__Example usage:__
```php
<?php
$client = new \vipnytt\RobotsTxtParser\TxtClient('http://example.com', 200, 'robots.txt');
```

### `Clean-param` directive
[Documentation](methods/CleanParamClient.md)

Array of dynamic URI parameters detected in an URI.
```php
$client->cleanParam()->detect('http://example.com?param1=value1&param2=value2');
```
Array of dynamic URI parameters detected in an URI. This func also includes an list of generic dynamic parameters, as well as any (optional) custom parameters.
```php
$client->cleanParam()->detectWithCommon('http://example.com?param1=value1&param2=value2');
```
List of dynamic URI parameters
```php
$client->cleanParam()->export();
```

### Export
[Documentation](methods/TxtClient.md)

Export all rules as an array
```php
$client->export();
```

### Get user-agents
[Documentation](methods/TxtClient.md)

Get an list of all declared User-agents
```php
$client->getUserAgents();
```

### `Host` directive
[Documentation](methods/HostClient.md)

Get the main host declared by the Host directive.
```php
$client->host()->export();
```
Get the main host declared by the Host directive. Falls back to the host of the effective URI if it isn't set
```php
$client->host()->getWithUriFallback();
```
Find out whether the host of the current URI also is the preferred one
```php
$client->host()->isPreferred();
```

### Render
[Documentation](methods/RenderClient.md)

Compatibility mode. Optimized for parsing by custom 3rd party parsers, witch do not follow the standards strictly.
```php
$client->render()->compatibility();
```
Compressed to a absolute minimum. Optimized for storage in ex. databases.
```php
$client->render()->compressed();
```
Normal looking robots.txt. Optimized for human readability, it's also the easiest to modify.
```php
$client->render()->normal();
```

### `Sitemap` directive
[Documentation](methods/SitemapClient.md)

Export an list of sitemaps
```php
$client->sitemap()->export();
```

### `Allow` directive
[Documentation](methods/AllowClient.md)

Export an array of the directives rules
```php
$client->userAgent('myBot')->allow()->export();
```
Check if the specified path is covered by this directive
```php
$client->userAgent('myBot')->allow()->hasPath('http://example.com/path/to/file');
```

### `Cache-delay` directive
[Documentation](methods/DelayClient.md)

Export the value of the directive
```php
$client->userAgent('myBot')->cacheDelay()->export();
```
Intended for usage by an 3rd party Delay handler
```php
$client->userAgent('myBot')->cacheDelay()->getBaseUri();
```
Intended for usage by an 3rd party Delay handler
```php
$client->userAgent('myBot')->cacheDelay()->getUserAgent();
```
Get the request-delay value
```php
$client->userAgent('myBot')->cacheDelay()->getValue();
```

### Handling of the `Cache-delay` directive
[Documentation](methods/DelayBaseInterface.md)

Get the size of the current request queue in seconds
```php
$client->userAgent('myBot')->cacheDelay()->handle($delayManager)->checkQueue();
```
Get the timestamp w/microseconds you'll have to wait until before sending the request
```php
$client->userAgent('myBot')->cacheDelay()->handle($delayManager)->getTimeSleepUntil();
```
Reset the queue for this host
```php
$client->userAgent('myBot')->cacheDelay()->handle($delayManager)->reset();
```
Sleep or delay the php processing until it's your turn to send the request
```php
$client->userAgent('myBot')->cacheDelay()->handle($delayManager)->sleep();
```

### `Comment` directive
[Documentation](methods/CommentClient.md)

Export an list of comments/messages/information that exists for the matching user-agent.
```php
$client->userAgent('myBot')->comment()->export();
```
Export an list of comments/messages/information that exists for your user-agent only. Spam-filtered and is intended to be read.
```php
$client->userAgent('myBot')->comment()->get();
```

### `Crawl-delay` directive
[Documentation](methods/DelayClient.md)

Export the value of the directive
```php
$client->userAgent('myBot')->crawlDelay()->export();
```
Intended for usage by an 3rd party Delay handler
```php
$client->userAgent('myBot')->crawlDelay()->getBaseUri();
```
Intended for usage by an 3rd party Delay handler
```php
$client->userAgent('myBot')->crawlDelay()->getUserAgent();
```
Get the request-delay value
```php
$client->userAgent('myBot')->crawlDelay()->getValue();
```

### Handling of the `Crawl-delay` directive
[Documentation](methods/DelayBaseInterface.md)

Get the size of the current request queue in seconds
```php
$client->userAgent('myBot')->crawlDelay()->handle($delayManager)->checkQueue();
```
Get the timestamp w/microseconds you'll have to wait until before sending the request
```php
$client->userAgent('myBot')->crawlDelay()->handle($delayManager)->getTimeSleepUntil();
```
Reset the queue for this host
```php
$client->userAgent('myBot')->crawlDelay()->handle($delayManager)->reset();
```
Sleep or delay the php processing until it's your turn to send the request
```php
$client->userAgent('myBot')->crawlDelay()->handle($delayManager)->sleep();
```

### `Disallow` directive
[Documentation](methods/AllowClient.md)

Export an array of the directives rules
```php
$client->userAgent('myBot')->disallow()->export();
```
Check if the specified path is covered by this directive
```php
$client->userAgent('myBot')->disallow()->hasPath('http://example.com/path/to/file');
```

### Export
[Documentation](methods/UserAgentClient.md)

Export an array of the rules for the selected User-agent
```php
$client->userAgent('myBot')->export();
```

### isAllowed
[Documentation](methods/UserAgentClient.md)

Check if an URI is allowed to crawl
```php
$client->userAgent('myBot')->isAllowed('http://example.com/path/to/file');
```

### isDisallowed
[Documentation](methods/UserAgentClient.md)

Check if an URI is disallowed to crawl
```php
$client->userAgent('myBot')->isDisallowed('http://example.com/path/to/file');
```

### `NoIndex` directive
[Documentation](methods/AllowClient.md)

Export an array of the directives rules
```php
$client->userAgent('myBot')->noIndex()->export();
```
Check if the specified path is covered by this directive
```php
$client->userAgent('myBot')->noIndex()->hasPath('http://example.com/path/to/file');
```

### `Request-rate` directive
[Documentation](methods/DelayClient.md)

Export an array of delays and their corresponding timestamps
```php
$client->userAgent('myBot')->requestRate()->export();
```
Intended for usage by an 3rd party Delay handler
```php
$client->userAgent('myBot')->requestRate()->getBaseUri();
```
Intended for usage by an 3rd party Delay handler
```php
$client->userAgent('myBot')->requestRate()->getUserAgent();
```
Get the request-delay value
```php
$client->userAgent('myBot')->requestRate()->getValue();
```

### Handling of the `Request-rate` directive
[Documentation](methods/DelayBaseInterface.md)

Get the size of the current request queue in seconds
```php
$client->userAgent('myBot')->requestRate()->handle($delayManager)->checkQueue();
```
Get the timestamp w/microseconds you'll have to wait until before sending the request
```php
$client->userAgent('myBot')->requestRate()->handle($delayManager)->getTimeSleepUntil();
```
Reset the queue for this host
```php
$client->userAgent('myBot')->requestRate()->handle($delayManager)->reset();
```
Sleep or delay the php processing until it's your turn to send the request
```php
$client->userAgent('myBot')->requestRate()->handle($delayManager)->sleep();
```

### `Robot-version` directive
[Documentation](methods/RobotVersionClient.md)

Exports the value of the directive
```php
$client->userAgent('myBot')->robotVersion()->export();
```

### `Visit-time` directive
[Documentation](methods/VisitTimeClient.md)

Export an list of visit-times in UTC
```php
$client->userAgent('myBot')->visitTime()->export();
```
Check if it's currently visiting time
```php
$client->userAgent('myBot')->visitTime()->isVisitTime();
```
