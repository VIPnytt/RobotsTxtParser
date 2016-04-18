<?php
namespace vipnytt\RobotsTxtParser\Modules\Directives;

use vipnytt\RobotsTxtParser\Exceptions\ParserException;
use vipnytt\RobotsTxtParser\Modules\Toolbox;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class CrawlDelay
 *
 * @package vipnytt\RobotsTxtParser\Modules\Directives
 */
class CrawlDelay implements DirectiveInterface, RobotsTxtInterface
{
    use Toolbox;

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
    protected $directive = self::DIRECTIVE_CRAWL_DELAY;

    /**
     * Delay
     * @var float|int
     */
    protected $value;

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
     * @param float|int|string $line
     * @return bool
     */
    public function add($line)
    {
        if (isset($this->value) && $this->value > 0) {
            return false;
        }
        if (empty($float = floatval($line))) {
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
