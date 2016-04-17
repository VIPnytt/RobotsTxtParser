<?php
namespace vipnytt\RobotsTxtParser\Directives;

use vipnytt\RobotsTxtParser\Exceptions\ParserException;
use vipnytt\RobotsTxtParser\ObjectTools;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class CrawlDelay
 *
 * @package vipnytt\RobotsTxtParser\Directives
 */
class CrawlDelay implements DirectiveInterface, RobotsTxtInterface
{
    use ObjectTools;

    /**
     * Directive alternatives
     */
    const DIRECTIVE = [
        self::DIRECTIVE_CACHE_DELAY,
        self::DIRECTIVE_CRAWL_DELAY,
    ];

    /**
     * Directive
     */
    protected $directive;

    /**
     * Delay array
     * @var array
     */
    protected $value = [];

    /**
     * CrawlDelay constructor.
     * @param string $directive
     * @throws ParserException
     */
    public function __construct($directive = self::DIRECTIVE_CRAWL_DELAY)
    {
        $this->directive = $this->validateDirective($directive, self::DIRECTIVE);
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        if (isset($this->value) && $this->value > 0) {
            return false;
        }
        if (empty(($float = floatval($line)))) {
            return false;
        }
        $this->value = $float;
        return true;
    }

    /**
     * Export
     *
     * @return array
     */
    public function export()
    {
        return empty($this->value) ? [] : [$this->directive => $this->value];
    }
}
