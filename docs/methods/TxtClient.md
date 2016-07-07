# Class TxtClient
```
@package vipnytt\RobotsTxtParser
```

## Public functions
- [cleanParam](#cleanparam)
- [export](#export)
- [getStatusCode](#getstatuscode)
- [getUserAgents](#getuseragents)
- [host](#host)
- [render](#render)
- [sitemap](#sitemap)
- [userAgent](#useragent)

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

### getStatusCode
```
@return int|null
```
Get the HTTP/FTP status code

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
