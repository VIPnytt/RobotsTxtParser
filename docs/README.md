# Getting started

- [What class to use?](#createanrobotstxtparserinstance)
- [Directives](directives.md)
- [Cheat sheet](#cheatsheet)

## Create an `robots.txt` parser instance
You have 3 different ways to construct the `robots.txt` parser, each suited for different demands.
### Automatic download
```php
$client = new \vipnytt\RobotsTxtParser\UriClient('http://example.com');
```
- [Documentation + additional UriClient specific methods](methods/UriClient.md).
- [Usage examples](#uriclient)

### Custom `robots.txt` input
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
- [Usage examples](#txtclient)

### The integrated caching system
```php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=database', 'username', 'password');
$handler = new \vipnytt\RobotsTxtParser\Cache($pdo);
$client = $handler->client('http://example.com');
```
- [Set-up instructions](sql/cache.md).
- [Documentation + special methods](methods/Cache.md).
- [Usage examples](#cache)

## The Delay handler
The Delay class is mainly for administration purposes, but may also be used as an alternative way to handle delays. It is generally not needed, but available usage examples are shown below.
```php
$delayHandler = new \vipnytt\RobotsTxtParser\Delay($pdo);
```
- [Set-up instructions](sql/delay.md).
- [Documentation + special methods](methods/Delay.md).
- [Usage examples](#delay)

## Cheat sheet
- [Cache](#cache)
- [Delay](#delay)
- [UriClient](#uriclient)
- [TxtClient](txtclient)

### Cache
[Cache documentation](methods/Cache.md)

Note: _Most parameters available is set to their default values and is not shown. Referer to the documentation for the specific class, for a full overview._
```php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=database', 'username', 'password');
$handler = new \vipnytt\RobotsTxtParser\Cache($pdo);
```
```php
// Clean the cache for unused robots.txt files
$handler->clean();
```
```php
// Update the cache for any active robots.txt files
$handler->cron();
```
```php
// Invalidate the cache for an specific URI
$handler->invalidate('http://example.com');
```
Create an [TxtClient](#txtclient). [Documentation](methods/TxtClient.md)
```php
// Create the TxtClient for parsing purposes
$client = $handler->client('http://example.com');
```

### Delay
[Delay documentation](methods/Delay.md)

Note: _Most parameters available is set to their default values and is not shown. Referer to the documentation for the specific class, for a full overview._
```php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=database', 'username', 'password');
$delayHandler = new \vipnytt\RobotsTxtParser\Delay($pdo);
```
```php
// Clean the delay storage for any outdated records
$delayHandler->clean();
```
```php
// Get an list of the hosts with highest wait-time.
$delayHandler->getTopWaitTimes();
```

Delay client. [Documentation](methods/DelayInterface.md)
```php
$txtClient = new \vipnytt\RobotsTxtParser\TxtClient('http://example.com', 200, 'robots.txt');
$delayInterface = $txtClient->userAgent('myBot')->crawlDelay();
$delayInterface = $txtClient->userAgent('myBot')->cacheDelay();
$delayInterface = $txtClient->userAgent('myBot')->requestRate();

// Delay client constructed from any DelayInterface class
$delayClient = $delayHandler->client($delayInterface);
```
```php
// Get the size of the current request queue in seconds
$delayClient->getQueue();
```
```php
// Get the timestamp w/microseconds you'll have to wait until before sending the request
$delayClient->getTimeSleepUntil();
```
```php
// Reset the global queue for this host
$delayClient->reset();
```
```php
// Sleep until it's your turn to send the request
$delayClient->sleep();
```

### UriClient
[UriClient documentation](methods/UriClient.md)

Note: _Most parameters available is set to their default values and is not shown. Referer to the documentation for the specific class, for a full overview._
```php
$client = new \vipnytt\RobotsTxtParser\UriClient('http://example.com');
```
```php
// Get base-URI
$client->getBaseUri();
```
```php
// Get the robots.txt contents
$client->getContents();
```
```php
// Get the effective base-URI (after any redirects)
$client->getEffectiveUri();
```
```php
// Get the character encoding
$client->getEncoding();
```
```php
// Get the HTTP status code (also works with FTP)
$client->getStatusCode();
```
```php
// Next-update timestamp
$client->nextUpdate();
```
```php
// The timestamp the robots.txt is valid until
$client->validUntil();
```

The `UriClient` extends the `TxtClient`. See [TxtClient](#txtclient) for the rest of the available methods.

### TxtClient
[TxtClient documentation](methods/TxtClient.md)

Note: _Most parameters available is set to their default values and is not shown. Referer to the documentation for the specific class, for a full overview._
```php
$client = new \vipnytt\RobotsTxtParser\TxtClient('http://example.com', 200, 'robots.txt');
```
`Clean-param` directive. [Documentation](methods/CleanParamClient.md)
```php
// List of dynamic URI parameters
$client->cleanParam()->export();

// Find out whether the URL contains dynamic URI parameters
$client->cleanParam()->isListed('http://example.com?param1=value1&param2=value2');
```
Export
```php
// Export all rules as an array
$client->export();
```
Get user-agents
```php
// Get an list of all declared User-agents
$client->getUserAgents();
```
`Host` directive. [Documentation](methods/HostClient.md)
```php
// Export the content of the Host directive(s)
$client->host()->export();

// Get the main host declared by the Host directive
$client->host()->get();

// Get the main host declared by the Host directive. Falls back to the host of the effective URI if it isn't set
$client->host()->getWithFallback();

// Find out whether the host of the current URI also is the preferred one
$client->host()->isPreferred();

// Find out whether the host of the specified URI is listed by the Host directive
$client->host()->isUriListed('http://example.com');
```
Render
```php
// Renders the parsed robots.txt file
$client->render();
```
`Sitemap` directive. [Documentation](methods/SitemapClient.md)
```php
// Export an list of sitemaps
$client->sitemap()->export();
```
`Allow` directive. [Documentation](methods/AllowClient.md)
```php
// Export an array of the directives rules
$client->userAgent('myBot')->allow()->export();

// Check if the specified path is covered by any of the directives rules
$client->userAgent('myBot')->allow()->isListed('http://example.com/path/to/file');
```
`Cache-delay` directive. [Documentation](methods/DelayClient.md)
```php
// Export the value of the directive
$client->userAgent('myBot')->cacheDelay()->export();

// Intended for usage by an 3rd party Delay handler
$client->userAgent('myBot')->cacheDelay()->getBaseUri();

// Intended for usage by an 3rd party Delay handler
$client->userAgent('myBot')->cacheDelay()->getUserAgent();

// Get the request-delay value
$client->userAgent('myBot')->cacheDelay()->getValue();
```
Handling of the `Cache-delay` directive. [Documentation](methods/DelayInterface.md)
```php
// Get the size of the current request queue in seconds
$client->userAgent('myBot')->cacheDelay()->handle($pdo)->getQueue();

// Get the timestamp w/microseconds you'll have to wait until before sending the request
$client->userAgent('myBot')->cacheDelay()->handle($pdo)->getTimeSleepUntil();

// Reset the global queue for this host
$client->userAgent('myBot')->cacheDelay()->handle($pdo)->reset();

// Sleep until it's your turn to send the request
$client->userAgent('myBot')->cacheDelay()->handle($pdo)->sleep();
```
`Comment` directive. [Documentation](methods/CommentClient.md)
```php
// Export an list of comments/messages/information that exists for the matching user-agent.
$client->userAgent('myBot')->comment()->export();

// Export an list of comments/messages/information that exists for your user-agent only. Spam-filtered, intended to be read.
$client->userAgent('myBot')->comment()->get();
```
`Crawl-delay` directive. [Documentation](methods/DelayClient.md)
```php
// Export the value of the directive
$client->userAgent('myBot')->crawlDelay()->export();

// Intended for usage by an 3rd party Delay handler
$client->userAgent('myBot')->crawlDelay()->getBaseUri();

// Intended for usage by an 3rd party Delay handler
$client->userAgent('myBot')->crawlDelay()->getUserAgent();

// Get the request-delay value
$client->userAgent('myBot')->crawlDelay()->getValue();
```
Handling of the `Crawl-delay` directive. [Documentation](methods/DelayInterface.md)
```php
// Get the size of the current request queue in seconds
$client->userAgent('myBot')->crawlDelay()->handle($pdo)->getQueue();

// Get the timestamp w/microseconds you'll have to wait until before sending the request
$client->userAgent('myBot')->crawlDelay()->handle($pdo)->getTimeSleepUntil();

// Reset the global queue for this host
$client->userAgent('myBot')->crawlDelay()->handle($pdo)->reset();

// Sleep until it's your turn to send the request
$client->userAgent('myBot')->crawlDelay()->handle($pdo)->sleep();
```
`Disallow` directive. [Documentation](methods/AllowClient.md)
```php
// Export an array of the directives rules
$client->userAgent('myBot')->disallow()->export();

// Check if the specified path is covered by any of the directives rules
$client->userAgent('myBot')->disallow()->isListed('http://example.com/path/to/file');
```
Export
```php
// Export an array of the rules for the selected User-agent
$client->userAgent('myBot')->export();
```
isAllowed
```php
// Check if an URI is allowed to crawl
$client->userAgent('myBot')->isAllowed('http://example.com/path/to/file');
```
isDisallowed
```php
// Check if an URI is disallowed to crawl
$client->userAgent('myBot')->isDisallowed('http://example.com/path/to/file');
```
`NoIndex` directive. [Documentation](methods/AllowClient.md)
```php
// Export an array of the directives rules
$client->userAgent('myBot')->noIndex()->export();

// Check if the specified path is covered by any of the directives rules
$client->userAgent('myBot')->noIndex()->isListed('http://example.com/path/to/file');
```
`Request-rate` directive. [Documentation](methods/DelayClient.md)
```php
// Export an array of delays and their corresponding timestamps
$client->userAgent('myBot')->requestRate()->export();

// Intended for usage by an 3rd party Delay handler
$client->userAgent('myBot')->requestRate()->getBaseUri();

// Intended for usage by an 3rd party Delay handler
$client->userAgent('myBot')->requestRate()->getUserAgent();

// Get the request-delay value
$client->userAgent('myBot')->requestRate()->getValue();
```
Handling of the `Request-rate` directive. [Documentation](methods/DelayInterface.md)
```php
// Get the size of the current request queue in seconds
$client->userAgent('myBot')->requestRate()->handle($pdo)->getQueue();

// Get the timestamp w/microseconds you'll have to wait until before sending the request
$client->userAgent('myBot')->requestRate()->handle($pdo)->getTimeSleepUntil();

// Reset the global queue for this host
$client->userAgent('myBot')->requestRate()->handle($pdo)->reset();

// Sleep until it's your turn to send the request
$client->userAgent('myBot')->requestRate()->handle($pdo)->sleep();
```
`Robot-version` directive. [Documentation](methods/RobotVersionClient.md)
```php
// Exports the value of the directive
$client->userAgent('myBot')->robotVersion()->export();
```
`Visit-time` directive. [Documentation](methods/VisitTimeClient.md)
```php
// Export an list of visit-times in UTC
$client->userAgent('myBot')->visitTime()->export();

// Check if it's currently visiting time
$client->userAgent('myBot')->visitTime()->isVisitTime();
```
