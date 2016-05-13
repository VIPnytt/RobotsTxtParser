# Directives

- [Allow](#Allow)
- [Cache-delay](#Cache-delay)
- [Clean-param](#Clean-param)
- [Comment](#Comment)
- [Crawl-delay](#Crawl-delay)
- [Disallow](#Disallow)
- [Host](#Host)
- [Request-rate](#Request-rate)
- [Robot-version](#Robot-version)
- [Sitemap](#Sitemap)
- [User-agent](#User-agent)
- [Visit-time](#Visit-time)

## Allow
The ``allow`` directive specifies paths that may be accessed by the designated crawlers. When no path is specified, the directive is ignored.

__Usage:__
````
allow: [path]
````

__See also:__
- [Disallow](#Disallow)

__References:__
- [Google's robots.txt specifications](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#allow)
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#allow-disallow)
- [The Web Robots Pages](http://www.robotstxt.org/robotstxt.html)
- [The Web Robots Pages, version 2.0 draft](http://www.conman.org/people/spc/robots2.html#format.directives.allow)

## Cache-delay
Identical to ``Crawl-delay``, except it applies to content caching only.

__Note:__ _This is an unofficial directive._

__See also:__
- [Crawl-delay](#Crawl-delay)
- [Request-rate](#Request-rate)

## Clean-param
If page addresses contain dynamic parameters that do not affect the content (e.g. identifiers of sessions, users, referrers etc.), they can be described using the ``Clean-param`` directive.

__Usage:__
````
Clean-param: p0[&p1&p2&..&pn] [path]
````

__References:__
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#clean-param)

## Comment
Comments witch are supposed to be sent back to the author/user of the robot. It can be used to eg. explain the robot policy of a site (say, that one government site that hates robots).

__Usage:__
````
comment: [text]
````

__References:__
- [The Web Robots Pages, version 2.0 draft](http://www.conman.org/people/spc/robots2.html#format.directives.comment)

## Crawl-delay
The ``Crawl-delay`` directive specifies the minimum interval (in seconds) for a robot to wait after loading one page, before starting to load another.

__Usage:__
````
crawl-delay: [seconds]
````

__See also:__
- [Cache-delay](#Cache-delay)
- [Request-rate](#Request-rate)

__References:__
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#crawl-delay)

## Disallow
The ``disallow`` directive specifies paths that must not be accessed by the designated crawlers. When no path is specified, the directive is ignored.

__Usage:__
````
disallow: [path]
````

__See also:__
- [Allow](#Allow)

__References:__
- [Google's robots.txt specifications](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#disallow)
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#allow-disallow)
- [The Web Robots Pages](http://www.robotstxt.org/robotstxt.html)
- [W3C Recommendation](https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1)
- [The Web Robots Pages, version 2.0 draft](http://www.conman.org/people/spc/robots2.html#format.directives.disallow)

## Host
If a site has mirrors, the ``host`` directive is used to indicate which site is main one.

__Usage:__
````
host: [host]
````

### Inline Host
The directive can also be used _inside_ other directives, eg. to disallow crawling of a mirror.
````
disallow: host: [host]
````

__See also:__
- [Allow](#Allow)
- [Disallow](#Disallow)

__References:__
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#host)

## Request-rate
The ``request-rate`` directive specifies the minimum time of how often a robot can request a page.

__Usage:__
````
request-rate: [rate]
````
````
request-rate: [rate] [time]-[time]
````

__See also:__
- [Cache-delay](#Cache-delay)
- [Crawl-delay](#Crawl-delay)
- [Visit-time](#Visit-time)

__References:__
- [The Web Robots Pages, version 2.0 draft](http://www.conman.org/people/spc/robots2.html#format.directives.request-rate)

## Robot-version
Witch _Robot exclusion standard_ version to use for parsing.
- [Version 1.0](http://www.robotstxt.org/robotstxt.html) (default)
- [Version 2.0 draft](http://www.conman.org/people/spc/robots2.html)

__Usage:__
````
robot-version: [version]
````

__Note:__ Due to the different _interpretations_ and _robot-specific_ extensions of the _Robot exclusion standard_, it has been suggested that the version number present is more for documentation purposes than for content negotiation.

__References:__
- [The Web Robots Pages, version 2.0 draft](http://www.conman.org/people/spc/robots2.html#format.directives.robot-version)

## Sitemap
The ``sitemap`` directive is used to list URL's witch describes the site structure.

__Usage:__
````
sitemap: [absoluteURL]
````

__References:__
- [Google's robots.txt specifications](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#sitemap)
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#sitemap)

## User-agent
The ``user-agent`` directive is used as an _start-of-group_ record, and specifies witch User-agent the following rules should be applied to.

__Usage:__
````
user-agent: [name]
````

__References:__
- [Google's robots.txt specifications](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#order-of-precedence-for-user-agents)
- [Yandex robots.txt specifications](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#user-agent)
- [The Web Robots Pages](http://www.robotstxt.org/robotstxt.html)
- [W3C Recommendation](https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1)
- [The Web Robots Pages, version 2.0 draft](http://www.conman.org/people/spc/robots2.html#format.directives.user-agent)

## Visit-time
The robot is requested to only visit the site inside the given ``visit-time`` window.

__Usage:__
````
visit-time: [time]-[time]
````

__See also:__
- [Request-rate](#Request-rate)

__References:__
- [The Web Robots Pages, version 2.0 draft](http://www.conman.org/people/spc/robots2.html#format.directives.visit-time)
