<?php
namespace vipnytt\RobotsTxtParser\Directives;

use vipnytt\RobotsTxtParser\ObjectTools;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class UserAgent
 *
 * @package vipnytt\RobotsTxtParser\Directives
 */
class UserAgent implements DirectiveInterface, RobotsTxtInterface
{
    use ObjectTools;

    const SUB_DIRECTIVES = [
        self::DIRECTIVE_ALLOW,
        self::DIRECTIVE_CACHE_DELAY,
        self::DIRECTIVE_CRAWL_DELAY,
        self::DIRECTIVE_DISALLOW,
    ];

    /**
     * Directive
     */
    const DIRECTIVE = 'User-agent';

    protected $userAgent = [];
    protected $userAgents = [];

    protected $parent;
    protected $array = [];

    protected $allow = [];
    protected $cacheDelay = [];
    protected $crawlDelay = [];
    protected $disallow = [];

    public function __construct($parent = null)
    {
        $this->set(self::USER_AGENT);
    }

    public function set($line, $append = false)
    {
        if (!$append) {
            $this->userAgent = [];
        }
        $this->userAgent[] = $line;
        if (!in_array($line, $this->userAgents)) {
            $this->allow[$line] = new Allow(self::DIRECTIVE);
            $this->cacheDelay[$line] = new CacheDelay(self::DIRECTIVE);
            $this->crawlDelay[$line] = new CrawlDelay(self::DIRECTIVE);
            $this->disallow[$line] = new Disallow(self::DIRECTIVE);
            $this->userAgents[] = $line;
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
        switch ($pair['directive']) {
            case self::DIRECTIVE_ALLOW:
                foreach ($this->userAgent as $userAgent) {
                    $result = $this->allow[$userAgent]->add($pair['value']);
                }
                return $result;
            case self::DIRECTIVE_CACHE_DELAY:
                foreach ($this->userAgent as $userAgent) {
                    $result = $this->cacheDelay[$userAgent]->add($pair['value']);
                }
                return $result;
            case self::DIRECTIVE_CRAWL_DELAY:
                foreach ($this->userAgent as $userAgent) {
                    $result = $this->crawlDelay[$userAgent]->add($pair['value']);
                }
                return $result;
            case self::DIRECTIVE_DISALLOW:
                foreach ($this->userAgent as $userAgent) {
                    $result = $this->disallow[$userAgent]->add($pair['value']);
                }
                return $result;
        }
        return false;
    }

    /**
     * Check rules
     *
     * @param  string $url - URL to check
     * @param  string $type - directive to check
     * @return bool
     */
    public function check($url, $type)
    {
        $result = ($type === self::DIRECTIVE_ALLOW);
        foreach ([self::DIRECTIVE_DISALLOW, self::DIRECTIVE_ALLOW] as $directive) {
            if ($this->$directive->check($url)) {
                $result = ($type === $directive);
            }
        }
        return $result;
    }

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
