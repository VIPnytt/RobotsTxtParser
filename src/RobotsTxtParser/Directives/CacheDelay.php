<?php
namespace vipnytt\RobotsTxtParser\Directives;

/**
 * Class CacheDelay
 *
 * @package vipnytt\RobotsTxtParser\Directives
 */
class CacheDelay implements DirectiveInterface
{
    /**
     * Directive
     */
    const DIRECTIVE = 'Cache-delay';

    protected $array = [];
    protected $parent;


    public function __construct($parent = null)
    {
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
