<?php
namespace vipnytt\RobotsTxtParser\Directives;

use vipnytt\RobotsTxtParser\ObjectTools;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class UserAgent
 *
 * @package vipnytt\RobotsTxtParser\Directives
 */
final class UserAgent implements DirectiveInterface, RobotsTxtInterface
{
    use ObjectTools;

    /**
     * Sub directives white list
     */
    const SUB_DIRECTIVES = [
        self::DIRECTIVE_ALLOW,
        self::DIRECTIVE_CACHE_DELAY,
        self::DIRECTIVE_CRAWL_DELAY,
        self::DIRECTIVE_DISALLOW,
    ];

    /**
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_USER_AGENT;
    
    /**
     * All User-agents declared
     * @var array
     */
    public $userAgents = [];
    /**
     * Sub-directive Allow
     * @var array
     */
    public $allow = [];
    /**
     * Sub-directive Cache-delay
     * @var array
     */
    public $cacheDelay = [];
    /**
     * Sub-directive Crawl-delay
     * @var array
     */
    public $crawlDelay = [];
    /**
     * Sub-directive Disallow
     * @var array
     */
    public $disallow = [];
    /**
     * Current User-agent(s)
     * @var array
     */
    protected $userAgent = [];

    /**
     * UserAgent constructor.
     */
    public function __construct()
    {
        $this->set();
    }

    /**
     * Set new User-agent
     *
     * @param array $array
     * @return bool
     */
    public function set($array = [self::USER_AGENT])
    {
        $this->userAgent = $array;
        foreach ($this->userAgent as $userAgent) {
            if (!in_array($userAgent, $this->userAgents)) {
                $this->allow[$userAgent] = new DisAllow(self::DIRECTIVE_ALLOW);
                $this->cacheDelay[$userAgent] = new CrawlDelay(self::DIRECTIVE_CACHE_DELAY);
                $this->crawlDelay[$userAgent] = new CrawlDelay(self::DIRECTIVE_CRAWL_DELAY);
                $this->disallow[$userAgent] = new DisAllow(self::DIRECTIVE_DISALLOW);
                $this->userAgents[] = $userAgent;
            }
        }
        return true;
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        $result = false;
        $pair = $this->generateRulePair($line, self::SUB_DIRECTIVES);
        foreach ($this->userAgent as $userAgent) {
            switch ($pair['directive']) {
                case self::DIRECTIVE_ALLOW:
                    $result = $this->allow[$userAgent]->add($pair['value']);
                    break;
                case self::DIRECTIVE_CACHE_DELAY:
                    $result = $this->cacheDelay[$userAgent]->add($pair['value']);
                    break;
                case self::DIRECTIVE_CRAWL_DELAY:
                    $result = $this->crawlDelay[$userAgent]->add($pair['value']);
                    break;
                case self::DIRECTIVE_DISALLOW:
                    $result = $this->disallow[$userAgent]->add($pair['value']);
                    break;
            }
        }
        return isset($result) ? $result : false;
    }

    /**
     * Export
     *
     * @return array
     */
    public function export()
    {
        $result = [];
        foreach ($this->userAgents as $userAgent) {
            $current = $this->allow[$userAgent]->export()
                + $this->cacheDelay[$userAgent]->export()
                + $this->crawlDelay[$userAgent]->export()
                + $this->disallow[$userAgent]->export();
            if (!empty($current)) {
                $result[$userAgent] = $current;
            }
        }
        return empty($result) ? [] : [self::DIRECTIVE => $result];
    }
}
