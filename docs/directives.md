# Directives
- [Allow](#allow)
- [Cache-delay](#cache-delay)
- [Clean-param](#clean-param)
- [Comment](#comment)
- [Crawl-delay](#crawl-delay)
- [Disallow](#disallow)
- [Host](#host)
- [NoIndex](#noindex)
- [Request-rate](#request-rate)
- [Robot-version](#robot-version)
- [Sitemap](#sitemap)
- [User-agent](#user-agent)
- [Visit-time](#visit-time)

## Allow
The ``allow`` directive specifies paths that may be accessed by the designated crawlers. When no path is specified, the directive is ignored.

__robots.txt:__
````
allow: [path]
````

__See also:__
- [Disallow](#disallow)
- [NoIndex](#noindex)

__References:__
- [Google robots.txt specifications](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#allow)
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#allow-disallow)
- [Sean Conner: _"An Extended Standard for Robot Exclusion"_](http://www.conman.org/people/spc/robots2.html#format.directives.allow)
- [Martijn Koster: _"A Method for Web Robots Control"_](http://www.robotstxt.org/norobots-rfc.txt)

## Cache-delay
The ``Cache-delay`` directive specifies the minimum interval (in seconds) for a robot to wait after caching one page, before starting to cache another.

__robots.txt:__
````
cache-delay: [seconds]
````

__Note:__ _This is an unofficial directive._

__Library specific:__ When the value is requested but not found, the value of ``Crawl-delay`` is returned, to maintain compatibility.

__See also:__
- [Crawl-delay](#crawl-delay)
- [Request-rate](#request-rate)

## Clean-param
If page addresses contain dynamic parameters that do not affect the content (e.g. identifiers of sessions, users, referrers etc.), they can be described using the ``Clean-param`` directive.

__robots.txt:__
````
clean-param: [parameter]
````
````
clean-param: [parameter] [path]
````
````
clean-param: [parameter1]&[parameter2]&[...]
````
````
clean-param: [parameter1]&[parameter2]&[...] [path]
````

__References:__
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#clean-param)

### Inline Clean-param
The directive can also be used _inside_ other directives, eg. to disallow crawling of pages with specific parameters.
````
disallow: clean-param: [parameter]
````
````
noindex: clean-param: [parameter]
````

__See also:__
- [Allow](#allow)
- [Disallow](#disallow)
- [NoIndex](#noindex)

__References:__
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#clean-param)

## Comment
Comments witch are supposed to be sent back to the author/user of the robot. It can be used to eg. provide contact information for white-listing requests, or even explain the robot policy of a site.

__robots.txt:__
````
comment: [text]
````

__References:__
- [Sean Conner: _"An Extended Standard for Robot Exclusion"_](http://www.conman.org/people/spc/robots2.html#format.directives.comment)

## Crawl-delay
The ``Crawl-delay`` directive specifies the minimum interval (in seconds) for a robot to wait after loading one page, before starting to load another.

__robots.txt:__
````
crawl-delay: [seconds]
````

__See also:__
- [Cache-delay](#cache-delay)
- [Request-rate](#request-rate)

__References:__
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#crawl-delay)

## Disallow
The ``disallow`` directive specifies paths that must not be accessed by the designated crawlers. When no path is specified, the directive is ignored.

__robots.txt:__
````
disallow: [path]
````

__See also:__
- [Allow](#allow)
- [NoIndex](#noindex)

__References:__
- [Google robots.txt specifications](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#disallow)
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#allow-disallow)
- [W3C Recommendation HTML 4.01 specification](https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1)
- [Sean Conner: _"An Extended Standard for Robot Exclusion"_](http://www.conman.org/people/spc/robots2.html#format.directives.disallow)
- [Martijn Koster: _"A Method for Web Robots Control"_](http://www.robotstxt.org/norobots-rfc.txt)
- [Martijn Koster: _"A Standard for Robot Exclusion"_](http://www.robotstxt.org/orig.html)

## Host
If a site has mirrors, the ``host`` directive is used to indicate which site is main one.

__robots.txt:__
````
host: [host]
````

### Inline Host
The directive can also be used _inside_ other directives, eg. to disallow crawling of a mirror.
````
disallow: host: [host]
````
````
noindex: host: [host]
````

__See also:__
- [Allow](#allow)
- [Disallow](#disallow)
- [NoIndex](#noindex)

__References:__
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#host)

## NoIndex
The ``noindex`` directive is used to completely remove all traces of any matching site url from the search-engines.
````
noindex: [path]
````

__See also:__
- [Allow](#allow)
- [Disallow](#disallow)

## Request-rate
The ``request-rate`` directive specifies the minimum time of how often a robot can request a page, along with timestamps in UTC.

__robots.txt:__
````
request-rate: [rate]
````
````
request-rate: [rate] [time]-[time]
````

__Library specific:__ When the value is requested but not found, the value of ``Crawl-delay`` is returned, to maintain compatibility.

__See also:__
- [Cache-delay](#cache-delay)
- [Crawl-delay](#crawl-delay)
- [Visit-time](#visit-time)

__References:__
- [Sean Conner: _"An Extended Standard for Robot Exclusion"_](http://www.conman.org/people/spc/robots2.html#format.directives.request-rate)

## Robot-version
Witch _Robot exclusion standard_ version to use for parsing.
- [1.0](http://www.robotstxt.org/robotstxt.html)
- [2.0](http://www.conman.org/people/spc/robots2.html) (draft)

__robots.txt:__
````
robot-version: [version]
````

__Note:__ Due to the different _interpretations_ and _robot-specific_ extensions of the _Robot exclusion standard_, it has been suggested that the version number present is more for documentation purposes than for content negotiation.

__References:__
- [Sean Conner: _"An Extended Standard for Robot Exclusion"_](http://www.conman.org/people/spc/robots2.html#format.directives.robot-version)

## Sitemap
The ``sitemap`` directive is used to list URL's witch describes the site structure.

__robots.txt:__
````
sitemap: [url]
````

__References:__
- [Google robots.txt specifications](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#sitemap)
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#sitemap)
- [Sitemaps.org protocol](http://www.sitemaps.org/protocol.html#submit_robots)

## User-agent
The ``user-agent`` directive is used as an _start-of-group_ record, and specifies witch User-agent(s) the following rules should be applied to.

__robots.txt:__
````
user-agent: [name]
````
````
user-agent: [name]/[version]
````

__References:__
- [Google robots.txt specifications](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#order-of-precedence-for-user-agents)
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#user-agent)
- [W3C Recommendation HTML 4.01 specification](https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1)
- [Sean Conner: _"An Extended Standard for Robot Exclusion"_](http://www.conman.org/people/spc/robots2.html#format.directives.user-agent)
- [Martijn Koster: _"A Method for Web Robots Control"_](http://www.robotstxt.org/norobots-rfc.txt)
- [Martijn Koster: _"A Standard for Robot Exclusion"_](http://www.robotstxt.org/orig.html)

## Visit-time
The robot is requested to only visit the site inside the given ``visit-time`` window.

__robots.txt:__
````
visit-time: [time]-[time]
````

__See also:__
- [Request-rate](#request-rate)

__References:__
- [Sean Conner: _"An Extended Standard for Robot Exclusion"_](http://www.conman.org/people/spc/robots2.html#format.directives.visit-time)
