# Cheat sheet
- [Cache](#cache)
- [Delay](#delay)
- [Import](#import)
- [UriClient](#uriclient)
- [TxtClient](#txtclient)

## Cache
[Cache documentation](methods/Cache.md)

Note: _Most parameters available is set to their default values and is not shown. Referer to the documentation for the specific class, for a full overview._

__Example usage:__
```php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=database', 'username', 'password');
$handler = new \vipnytt\RobotsTxtParser\Cache($pdo);
```
Clean the cache for unused robots.txt files
```php
$handler->clean();
```
Update the cache for any active robots.txt files
```php
$handler->cron();
```
Invalidate the cache for an specific URI
```php
$handler->debug('http://example.com');
```
Get the RAW data from the database.
```php
$handler->invalidate('http://example.com');
```
### Create an [TxtClient](#txtclient)
[Documentation](methods/TxtClient.md)

Create the TxtClient for parsing purposes
```php
$client = $handler->client('http://example.com');
```

## Delay
[Delay documentation](methods/Delay.md)

Note: _Most parameters available is set to their default values and is not shown. Referer to the documentation for the specific class, for a full overview._

__Example usage:__
```php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=database', 'username', 'password');
$delayHandler = new \vipnytt\RobotsTxtParser\Delay($pdo);
```
Clean the delay storage for any outdated records
```php
$delayHandler->clean();
```
Get an list of the hosts with highest wait-time.
```php
$handler->debug('http://example.com');
```
Get the RAW data from the database.
```php
$delayHandler->getTopWaitTimes();
```

### Delay client
[Documentation](methods/DelayInterface.md)

__Example usage:__
```php
$txtClient = new \vipnytt\RobotsTxtParser\TxtClient('http://example.com', 200, 'robots.txt');

$delayInterface = $txtClient->userAgent('myBot')->crawlDelay();
// or
$delayInterface = $txtClient->userAgent('myBot')->cacheDelay();
// or
$delayInterface = $txtClient->userAgent('myBot')->requestRate();

// Delay client constructed from any DelayInterface class
$delayClient = $delayHandler->client($delayInterface);
```
Check the current request queue, returns the number of seconds of expected delay/sleep time.
```php
$delayClient->checkQueue();
```
Get the timestamp w/microseconds you'll have to wait until before sending the request
```php
$delayClient->getTimeSleepUntil();
```
Reset the global queue for this host
```php
$delayClient->reset();
```
Sleep until it's your turn to send the request
```php
$delayClient->sleep();
```

## Import
[Documentation](methods/Import.md)

__Example usage:__
```php
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

Compatibility mode. Optimized for parsing by custom 3rd party parsers, witch do not follow the standards.
```php
$client->render()->compatibility();
```
Compressed to a absolute minimum. Optimized for storage in databases.
```php
$client->render()->compressed();
```
Normal looking robots.txt. Optimized for human readability, and is also the easiest to modify.
```php
$client->render()->normal();
```
Minimal robots.txt. Same as normal, but without the eye candy.
```php
$client->render()->minimal();
```

### `Sitemap` directive
[Documentation](methods/SitemapClient.md)

Export an list of sitemaps
```php
$client->sitemap()->export();
```

### `Allow` directive
[Documentation](methods/AllowClient.md)

Check if an uri has parameters that makes it allowed to crawl
```php
$client->userAgent('myBot')->allow()->cleanParam()->detect('http://example.com/?ref=google');
```
Export an array of inline Clean-param parameters and paths
```php
$client->userAgent('myBot')->allow()->cleanParam()->export();
```
Export an array of the directives rules
```php
$client->userAgent('myBot')->allow()->export();
```
Export an array of inline Host hosts
```php
$client->userAgent('myBot')->allow()->host()->export();
```
Check if the specified path is covered by any of the directives rules
```php
$client->userAgent('myBot')->allow()->isListed('http://example.com/path/to/file');
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
[Documentation](methods/DelayInterface.md)

Get the size of the current request queue in seconds
```php
$client->userAgent('myBot')->cacheDelay()->handle($pdo)->checkQueue();
```
Get the timestamp w/microseconds you'll have to wait until before sending the request
```php
$client->userAgent('myBot')->cacheDelay()->handle($pdo)->getTimeSleepUntil();
```
Reset the global queue for this host
```php
$client->userAgent('myBot')->cacheDelay()->handle($pdo)->reset();
```
Sleep until it's your turn to send the request
```php
$client->userAgent('myBot')->cacheDelay()->handle($pdo)->sleep();
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
[Documentation](methods/DelayInterface.md)

Get the size of the current request queue in seconds
```php
$client->userAgent('myBot')->crawlDelay()->handle($pdo)->checkQueue();
```
Get the timestamp w/microseconds you'll have to wait until before sending the request
```php
$client->userAgent('myBot')->crawlDelay()->handle($pdo)->getTimeSleepUntil();
```
Reset the global queue for this host
```php
$client->userAgent('myBot')->crawlDelay()->handle($pdo)->reset();
```
Sleep until it's your turn to send the request
```php
$client->userAgent('myBot')->crawlDelay()->handle($pdo)->sleep();
```

### `Disallow` directive
[Documentation](methods/AllowClient.md)

Check if an uri has parameters that makes it disallowed to crawl
```php
$client->userAgent('myBot')->disallow()->cleanParam()->detect('http://example.com/?ref=google');
```
Export an array of inline Clean-param parameters and paths
```php
$client->userAgent('myBot')->disallow()->cleanParam()->export();
```
Export an array of the directives rules
```php
$client->userAgent('myBot')->disallow()->export();
```
Export an array of inline Host hosts
```php
$client->userAgent('myBot')->disallow()->host()->export();
```
Check if the specified path is covered by any of the directives rules
```php
$client->userAgent('myBot')->disallow()->isListed('http://example.com/path/to/file');
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

Check if an uri has parameters that is denied to crawl
```php
$client->userAgent('myBot')->noIndex()->cleanParam()->detect('http://example.com/?ref=google');
```
Export an array of inline Clean-param parameters and paths
```php
$client->userAgent('myBot')->noIndex()->cleanParam()->export();
```
Export an array of the directives rules
```php
$client->userAgent('myBot')->noIndex()->export();
```
Export an array of inline Host hosts
```php
$client->userAgent('myBot')->noIndex()->host()->export();
```
Check if the specified path is covered by any of the directives rules
```php
$client->userAgent('myBot')->noIndex()->isListed('http://example.com/path/to/file');
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
[Documentation](methods/DelayInterface.md)

Get the size of the current request queue in seconds
```php
$client->userAgent('myBot')->requestRate()->handle($pdo)->checkQueue();
```
Get the timestamp w/microseconds you'll have to wait until before sending the request
```php
$client->userAgent('myBot')->requestRate()->handle($pdo)->getTimeSleepUntil();
```
Reset the global queue for this host
```php
$client->userAgent('myBot')->requestRate()->handle($pdo)->reset();
```
Sleep until it's your turn to send the request
```php
$client->userAgent('myBot')->requestRate()->handle($pdo)->sleep();
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
