<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Handler\Directives;

use vipnytt\RobotsTxtParser\Parser\Directives\AllowParser;
use vipnytt\RobotsTxtParser\Parser\Directives\CommentParser;
use vipnytt\RobotsTxtParser\Parser\Directives\DelayParser;
use vipnytt\RobotsTxtParser\Parser\Directives\RequestRateParser;
use vipnytt\RobotsTxtParser\Parser\Directives\RobotVersionParser;
use vipnytt\RobotsTxtParser\Parser\Directives\VisitTimeParser;
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
     * @var AllowParser
     */
    private $allow;

    /**
     * Cache-delay
     * @var DelayParser
     */
    private $cacheDelay;

    /**
     * Comment
     * @var CommentParser
     */
    private $comment;

    /**
     * Crawl-delay
     * @var DelayParser
     */
    private $crawlDelay;

    /**
     * Disallow
     * @var AllowParser
     */
    private $disallow;

    /**
     * NoIndex
     * @var AllowParser
     */
    private $noIndex;

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
     * @param string $effective
     * @param string $userAgent
     */
    public function __construct($base, $effective, $userAgent)
    {
        $this->allow = new AllowParser($base, $effective, self::DIRECTIVE_ALLOW);
        $this->cacheDelay = new DelayParser($base, self::DIRECTIVE_CACHE_DELAY);
        $this->comment = new CommentParser($base, $userAgent);
        $this->crawlDelay = new DelayParser($base, self::DIRECTIVE_CRAWL_DELAY);
        $this->disallow = new AllowParser($base, $effective, self::DIRECTIVE_DISALLOW);
        $this->noIndex = new AllowParser($base, $effective, self::DIRECTIVE_NO_INDEX);
        $this->requestRate = new RequestRateParser($base);
        $this->robotVersion = new RobotVersionParser();
        $this->visitTime = new VisitTimeParser();
    }

    /**
     * Allow
     *
     * @return AllowParser
     */
    public function allow()
    {
        return $this->allow;
    }

    /**
     * Cache-delay
     *
     * @return DelayParser
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
     * @return DelayParser
     */
    public function crawlDelay()
    {
        return $this->crawlDelay;
    }

    /**
     * Disallow
     *
     * @return AllowParser
     */
    public function disallow()
    {
        return $this->disallow;
    }

    /**
     * NoIndex
     *
     * @return AllowParser
     */
    public function noIndex()
    {
        return $this->noIndex;
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
