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
     * Rules added count
     * @var int
     */
    public $count = 0;

    /**
     * Rule set group name
     * @var string
     */
    public $group;

    /**
     * Allow
     * @var AllowParser
     */
    public $allow;

    /**
     * Cache-delay
     * @var DelayParser
     */
    public $cacheDelay;

    /**
     * Comment
     * @var CommentParser
     */
    public $comment;

    /**
     * Crawl-delay
     * @var DelayParser
     */
    public $crawlDelay;

    /**
     * Disallow
     * @var AllowParser
     */
    public $disallow;

    /**
     * NoIndex
     * @var AllowParser
     */
    public $noIndex;

    /**
     * Request-rate
     * @var RequestRateParser
     */
    public $requestRate;

    /**
     * Robot-version
     * @var RobotVersionParser
     */
    public $robotVersion;

    /**
     * Visit-time
     * @var VisitTimeParser
     */
    public $visitTime;

    /**
     * SubDirectiveHandler constructor.
     *
     * @param string $base
     * @param string $group
     */
    public function __construct($base, $group)
    {
        $this->group = $group;
        $this->allow = new AllowParser(self::DIRECTIVE_ALLOW);
        $this->cacheDelay = new DelayParser($base, self::DIRECTIVE_CACHE_DELAY);
        $this->comment = new CommentParser($this->group);
        $this->crawlDelay = new DelayParser($base, self::DIRECTIVE_CRAWL_DELAY);
        $this->disallow = new AllowParser(self::DIRECTIVE_DISALLOW);
        $this->noIndex = new AllowParser(self::DIRECTIVE_NO_INDEX);
        $this->requestRate = new RequestRateParser($base);
        $this->robotVersion = new RobotVersionParser();
        $this->visitTime = new VisitTimeParser();
    }
}
