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

    protected $parent;
    protected $array = [];

    protected $allow;
    protected $cacheDelay;
    protected $crawlDelay;
    protected $disallow;

    public function __construct($array, $parent = null)
    {
        $this->array = $array;
        $this->allow = new Allow([], self::DIRECTIVE);
        $this->cacheDelay = new CacheDelay([], self::DIRECTIVE);
        $this->crawlDelay = new CrawlDelay([], self::DIRECTIVE);
        $this->disallow = new Disallow([], self::DIRECTIVE);
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        $pair = $this->generateRulePair($line, self::SUB_DIRECTIVES);
        switch ($pair['directive']) {
            case self::DIRECTIVE_ALLOW:
                return $this->allow->add($pair['value']);
            case self::DIRECTIVE_CACHE_DELAY:
                return $this->cacheDelay->add($pair['value']);
            case self::DIRECTIVE_CRAWL_DELAY:
                return $this->crawlDelay->add($pair['value']);
            case self::DIRECTIVE_DISALLOW:
                return $this->disallow->add($pair['value']);
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
        $result = $this->array
            + $this->allow->export()
            + $this->cacheDelay->export()
            + $this->crawlDelay->export()
            + $this->disallow->export();
        return empty($result) ? [] : [self::DIRECTIVE => $result];
    }
}
