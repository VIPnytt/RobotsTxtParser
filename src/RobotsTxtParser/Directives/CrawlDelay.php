<?php
namespace vipnytt\RobotsTxtParser\Directives;

use vipnytt\RobotsTxtParser\Exceptions\ParserException;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class CrawlDelay
 *
 * @package vipnytt\RobotsTxtParser\Directives
 */
class CrawlDelay implements DirectiveInterface, RobotsTxtInterface
{
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
    protected $array = [];

    /**
     * CrawlDelay constructor.
     * @param string $directive
     * @throws ParserException
     */
    public function __construct($directive = self::DIRECTIVE_CRAWL_DELAY)
    {
        if (!in_array($directive, self::DIRECTIVE, true)) {
            throw new ParserException('Directive not allowed here, has to be `' . self::DIRECTIVE_CRAWL_DELAY . '` or `' . self::DIRECTIVE_CACHE_DELAY . '`');
        }
        $this->directive = mb_strtolower($directive);
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

    /**
     * Export
     *
     * @return array
     */
    public function export()
    {
        return empty($this->array) ? [] : [$this->directive => $this->array];
    }
}
