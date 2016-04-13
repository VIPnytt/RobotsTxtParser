<?php
namespace vipnytt\RobotsTxtParser\Directives;

/**
 * Class CrawlDelay
 *
 * @package vipnytt\RobotsTxtParser\Directives
 */
class CrawlDelay implements DirectiveInterface
{
    /**
     * Directive
     */
    const DIRECTIVE = 'Crawl-delay';

    protected $array = [];
    protected $parent;


    public function __construct($array, $parent = null)
    {
        $this->array = $array;
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        if (empty(($float = floatval($this->array)))) {
            return false;
        }
        $this->array = [$float];
        return true;
    }

    public function export()
    {
        return empty($this->array) ? [] : [self::DIRECTIVE => $this->array];
    }
}
