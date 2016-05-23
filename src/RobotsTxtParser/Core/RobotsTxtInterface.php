<?php
namespace vipnytt\RobotsTxtParser\Core;

/**
 * Interface RobotsTxtInterface
 *
 * @package vipnytt\RobotsTxtParser\Core
 */
interface RobotsTxtInterface
{
    /**
     * Robots.txt path
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#file-location--range-of-validity
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
     * @link http://www.ietf.org/rfc/rfc1945.txt
     */
    const MAX_REDIRECTS = 5;

    /**
     * Expected encoding
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#file-format
     */
    const ENCODING = 'UTF-8';

    /**
     * Robots.txt max length in bytes
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#file-format
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#additional-info
     */
    const BYTE_LIMIT = 64000;

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
     * @link http://www.robotstxt.org/robotstxt.html
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.allow
     */
    const DIRECTIVE_ALLOW = 'allow';

    /**
     * Directive: Cache-delay
     *
     * Unofficial
     * Identical to Crawl-delay, with one exception, applies when caching content only
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
     * @link http://www.robotstxt.org/robotstxt.html
     * @link https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.disallow
     */
    const DIRECTIVE_DISALLOW = 'disallow';

    /**
     * Directive: Host
     *
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#host
     */
    const DIRECTIVE_HOST = 'host';

    /**
     * Directive: Request-rate
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
     */
    const DIRECTIVE_SITEMAP = 'sitemap';

    /**
     * Directive: User-Agent
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#order-of-precedence-for-user-agents
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#user-agent
     * @link http://www.robotstxt.org/robotstxt.html
     * @link https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.user-agent
     */
    const DIRECTIVE_USER_AGENT = 'user-agent';

    /**
     * Directive: Visit-time
     *
     * @link http://www.conman.org/people/spc/robots2.html#format.directives.visit-time
     */
    const DIRECTIVE_VISIT_TIME = 'visit-time';
}
