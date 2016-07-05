<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\DelayClient;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class DelayParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class DelayParser implements ParserInterface, RobotsTxtInterface
{
    use DirectiveParserCommons;

    /**
     * Directive
     * @var string
     */
    private $directive;

    /**
     * Base uri
     * @var string
     */
    private $base;

    /**
     * Delay
     * @var float|int
     */
    private $delay;

    /**
     * DelayParser constructor.
     *
     * @param string $base
     * @param string $directive
     */
    public function __construct($base, $directive)
    {
        $this->base = $base;
        $this->directive = $this->validateDirective($directive, [self::DIRECTIVE_CRAWL_DELAY, self::DIRECTIVE_CACHE_DELAY]);
    }

    /**
     * Add
     *
     * @param float|int|string $line
     * @return bool
     */
    public function add($line)
    {
        if (
            !is_numeric($line) ||
            (
                isset($this->delay) &&
                $this->delay > 0
            )
        ) {
            return false;
        }
        // PHP hack to convert numeric string to float or int
        // http://stackoverflow.com/questions/16606364/php-cast-string-to-either-int-or-float
        $this->delay = $line + 0;
        return true;
    }

    /**
     * Client
     *
     * @param string $userAgent
     * @param float|int $fallbackValue
     * @return DelayClient
     */
    public function client($userAgent = self::USER_AGENT, $fallbackValue = 0)
    {
        return new DelayClient($this->base, $userAgent, $this->delay, $fallbackValue);
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        return empty($this->delay) ? [] : [$this->directive . ':' . $this->delay];
    }
}
