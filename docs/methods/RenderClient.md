# Class RenderClient
```php
@package vipnytt\RobotsTxtParser\Client
```

## Public functions
- [compatibility](#compatibility)
- [compressed](#compressed)
- [minimal](#minimal)
- [normal](#normal)

### compatibility
```php
@param string $lineSeparator
@return string
```
Generates an robots.txt optimized for parsing by custom 3rd party parsers, witch do not follow the standards.

Differences compared to [minimal](#minimal):
- Each User-agent is listed with it's own separate rule set (even if it's equal to others)
- Byte consuming, may be multiple times larger (depending on the number of user-agents)
- Maximum compatibility, optimized for badly written 3rd party parsers with limited specification support

### compressed
```php
@param string $lineSeparator
@return string
```
Generates an robots.txt compressed to a absolute minimum.

Best suited for parsing purposes and storage in databases.

### normal
```php
@param string $lineSeparator
@return string
```
Generates an normal looking robots.txt.

Differences compared to [compressed](#compressed):
- Maximum human readability
- Easy to edit
- User-agent groups are separated with blank lines

### minimal
```php
@param string $lineSeparator
@return string
```
Generates an minimal but normal looking robots.txt.

Differences compared to [compressed](#compressed):
- The directives first character is uppercase
- Whitespace between the directive and it's value
