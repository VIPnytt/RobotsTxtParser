<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class SubDirectiveHandler
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class SubDirectiveHandler implements RobotsTxtInterface
{
    /**
     * Allow
     * @var DisAllowParser
     */
    private $allow;

    /**
     * Cache-delay
     * @var CrawlDelayParser
     */
    private $cacheDelay;

    /**
     * Comment
     * @var CommentParser
     */
    private $comment;

    /**
     * Crawl-delay
     * @var CrawlDelayParser
     */
    private $crawlDelay;

    /**
     * Disallow
     * @var DisAllowParser
     */
    private $disallow;

    /**
     * Request-rate
     * @var RequestRateParser
     */
    private $requestRate;

    /**
     * Robot-version
     * @var RobotVersionParser
     */
    private $robotVersion;

    /**
     * Visit-time
     * @var VisitTimeParser
     */
    private $visitTime;

    /**
     * SubDirectiveHandler constructor.
     *
     * @param string $base
     * @param string $userAgent
     */
    public function __construct($base, $userAgent)
    {
        $this->allow = new DisAllowParser($base, $userAgent, self::DIRECTIVE_ALLOW);
        $this->cacheDelay = new CrawlDelayParser($base, $userAgent, self::DIRECTIVE_CACHE_DELAY);
        $this->comment = new CommentParser($base, $userAgent);
        $this->crawlDelay = new CrawlDelayParser($base, $userAgent, self::DIRECTIVE_CRAWL_DELAY);
        $this->disallow = new DisAllowParser($base, $userAgent, self::DIRECTIVE_DISALLOW);
        $this->requestRate = new RequestRateParser($base, $userAgent);
        $this->robotVersion = new RobotVersionParser();
        $this->visitTime = new VisitTimeParser();
    }

    /**
     * Allow
     *
     * @return DisAllowParser
     */
    public function allow()
    {
        return $this->allow;
    }

    /**
     * Cache-delay
     *
     * @return CrawlDelayParser
     */
    public function cacheDelay()
    {
        return $this->cacheDelay;
    }

    /**
     * Comment
     *
     * @return CommentParser
     */
    public function comment()
    {
        return $this->comment;
    }

    /**
     * Crawl-delay
     *
     * @return CrawlDelayParser
     */
    public function crawlDelay()
    {
        return $this->crawlDelay;
    }

    /**
     * Disallow
     *
     * @return DisAllowParser
     */
    public function disallow()
    {
        return $this->disallow;
    }

    /**
     * Request-rate
     *
     * @return RequestRateParser
     */
    public function requestRate()
    {
        return $this->requestRate;
    }

    /**
     * Robot-version
     *
     * @return RobotVersionParser
     */
    public function robotVersion()
    {
        return $this->robotVersion;
    }

    /**
     * Visit-time
     *
     * @return VisitTimeParser
     */
    public function visitTime()
    {
        return $this->visitTime;
    }
}
