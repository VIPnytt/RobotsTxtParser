<?php
namespace vipnytt\RobotsTxtParser;

/**
 * Interface RobotsTxtInterface
 *
 * @package vipnytt\RobotsTxtParser
 */
interface RobotsTxtInterface
{
    /**
     * Expected encoding
     */
    const ENCODING = 'UTF-8';

    /**
     * Robots.txt max length in bytes
     */
    const BYTE_LIMIT = 500000;

    /**
     * Max rule length
     */
    const MAX_LENGTH_RULE = 500;

    /**
     * Default User-Agent
     */
    const USER_AGENT = '*';

    /**
     * Allow
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#allow
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#allow-disallow
     * @link http://www.robotstxt.org/robotstxt.html
     */
    const DIRECTIVE_ALLOW = 'allow';

    /**
     * Clean-param
     *
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#clean-param
     */
    const DIRECTIVE_CLEAN_PARAM = 'clean-param';

    /**
     * Crawl-delay
     *
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#crawl-delay
     */
    const DIRECTIVE_CRAWL_DELAY = 'crawl-delay';

    /**
     * Disallow
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#disallow
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#allow-disallow
     * @link http://www.robotstxt.org/robotstxt.html
     * @link https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1
     */
    const DIRECTIVE_DISALLOW = 'disallow';

    /**
     * Host
     *
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#host
     */
    const DIRECTIVE_HOST = 'host';

    /**
     * Sitemap
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#sitemap
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#sitemap
     */
    const DIRECTIVE_SITEMAP = 'sitemap';

    /**
     * User-Agent
     *
     * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt#order-of-precedence-for-user-agents
     * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml#user-agent
     * @link http://www.robotstxt.org/robotstxt.html
     * @link https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1
     */
    const DIRECTIVE_USER_AGENT = 'user-agent';

    /**
     * Cache-delay
     *
     * Unofficial
     * Identical to Crawl-delay, with one exception, applies when caching content only
     */
    const DIRECTIVE_CACHE_DELAY = 'cache-delay';
}
