# Class TxtClient
```
@package vipnytt\RobotsTxtParser
```

## Public functions
- [__construct](#__construct)
- [cleanParam](#cleanparam)
- [export](#export)
- [getUserAgents](#getuseragents)
- [host](#host)
- [render](#render)
- [sitemap](#sitemap)
- [userAgent](#useragent)

### __construct
```
@param string $baseUri
@param int $statusCode
@param string $content
@param string $encoding
@param string|null $effectiveUri
@param int|null $byteLimit
```

### cleanParam
```
@return CleanParamClient
```
Wrapper for the [Clean-param](../directives.md#clean-param) directive.

Returns an instance of [CleanParamClient](CleanParamClient.md).

### export
```
@return array
```
Returns an tree-formatted array containing every single rule. Perfect for external usage.

### getUserAgents
```
@return string[]
```
List all user-agents defined in the `robots.txt`.

### host
```
@return HostClient
```
Wrapper for the [Host](../directives.md#host) directive.

Returns an instance of [HostClient](HostClient.md).

### render
```
@param string $lineSeparator
@return string
```
Returns an optimized and rendered copy of the original `robots.txt` file.

### sitemap
```
@return SitemapClient
```
Wrapper for the [Sitemap](../directives.md#sitemap) directive.

Returns an instance of [SitemapClient](SitemapClient.md).

### userAgent
```
@param string $string
@return UserAgentClient
```
Wrapper for the [User-agent](../directives.md#user-agent) directive.

Returns an instance of [UserAgentClient](UserAgentClient.md).
