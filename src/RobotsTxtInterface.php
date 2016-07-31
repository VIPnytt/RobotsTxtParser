<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser;

/**
 * Interface RobotsTxtInterface
 *
 * @package vipnytt\RobotsTxtParser
 */
interface RobotsTxtInterface
{
    /**
     * Robots.txt path
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#file-location--range-of-validity
     * @link https://tools.ietf.org/html/rfc3986
     * @link https://tools.ietf.org/html/rfc1808
     */
    const PATH = '/robots.txt';

    /**
     * Cache time
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#handling-http-result-codes
     */
    const CACHE_TIME = 86400;

    /**
     * Max redirects
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#handling-http-result-codes
     * @link https://tools.ietf.org/html/rfc1945
     */
    const MAX_REDIRECTS = 5;

    /**
     * Expected encoding
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#file-format
     * @link https://tools.ietf.org/html/rfc3986
     */
    const ENCODING = 'UTF-8';

    /**
     * Robots.txt max length in bytes
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#file-format
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#additional-info
     */
    const BYTE_LIMIT = 65535; // 524.280 bits or ~65 kilobytes

    /**
     * Max rule length
     *
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#clean-param
     */
    const MAX_LENGTH_RULE = 500;

    /**
     * Default User-Agent
     *
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#user-agent
     */
    const USER_AGENT = '*';

    /**
     * Directive: Allow
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#allow
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#allow-disallow
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.allow
     * @link http://www.robotstxt.org/norobots-rfc.txt
     */
    const DIRECTIVE_ALLOW = 'allow';

    /**
     * Directive: Cache-delay
     *
     * Unofficial
     * Used as an crawl-delay alternative specifically for caching purposes.
     */
    const DIRECTIVE_CACHE_DELAY = 'cache-delay';

    /**
     * Directive: Clean-param
     *
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#clean-param
     */
    const DIRECTIVE_CLEAN_PARAM = 'clean-param';

    /**
     * Directive: Comment
     *
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.comment
     */
    const DIRECTIVE_COMMENT = 'comment';

    /**
     * Directive: Crawl-delay
     *
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#crawl-delay
     */
    const DIRECTIVE_CRAWL_DELAY = 'crawl-delay';

    /**
     * Directive: Disallow
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#disallow
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#allow-disallow
     * @link https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.disallow
     * @link http://www.robotstxt.org/norobots-rfc.txt
     * @link http://www.robotstxt.org/orig.html
     */
    const DIRECTIVE_DISALLOW = 'disallow';

    /**
     * Directive: Host
     *
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#host
     * @link https://tools.ietf.org/html/rfc952
     */
    const DIRECTIVE_HOST = 'host';

    /**
     * Directive: NoIndex
     */
    const DIRECTIVE_NO_INDEX = 'noindex';

    /**
     * Directive: RequestClient-rate
     *
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.request-rate
     */
    const DIRECTIVE_REQUEST_RATE = 'request-rate';

    /**
     * Directive: Robot-version
     *
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.robot-version
     */
    const DIRECTIVE_ROBOT_VERSION = 'robot-version';

    /**
     * Directive: Sitemap
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#sitemap
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#sitemap
     * @link http://www.sitemaps.org/protocol.html#submit_robots
     */
    const DIRECTIVE_SITEMAP = 'sitemap';

    /**
     * Directive: User-Agent
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#order-of-precedence-for-user-agents
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#user-agent
     * @link https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.user-agent
     * @link http://www.robotstxt.org/norobots-rfc.txt
     * @link http://www.robotstxt.org/orig.html
     */
    const DIRECTIVE_USER_AGENT = 'user-agent';

    /**
     * Directive: Visit-time
     *
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.visit-time
     */
    const DIRECTIVE_VISIT_TIME = 'visit-time';

    /**
     * Directive aliases (for simple errors / typos)
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#file-format
     */
    const ALIAS_DIRECTIVES = [
        'cachedelay' => self::DIRECTIVE_CACHE_DELAY,
        'cleanparam' => self::DIRECTIVE_CLEAN_PARAM,
        'crawldelay' => self::DIRECTIVE_CRAWL_DELAY,
        'no-index' => self::DIRECTIVE_NO_INDEX,
        'requestrate' => self::DIRECTIVE_REQUEST_RATE,
        'robotversion' => self::DIRECTIVE_ROBOT_VERSION,
        'useragent' => self::DIRECTIVE_USER_AGENT,
        'visittime' => self::DIRECTIVE_VISIT_TIME,
    ];
}
