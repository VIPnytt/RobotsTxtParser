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
     * Base Uri
     * @var string
     */
    private $base;

    /**
     * User-agent
     * @var string
     */
    private $userAgent;

    /**
     * Delay
     * @var float|int
     */
    private $value;

    /**
     * DelayParser constructor.
     *
     * @param string $base
     * @param string $userAgent
     * @param string $directive
     */
    public function __construct($base, $userAgent, $directive)
    {
        $this->base = $base;
        $this->userAgent = $userAgent;
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
                isset($this->value) &&
                $this->value > 0
            )
        ) {
            return false;
        }
        // PHP hack to convert numeric string to float or int
        // http://stackoverflow.com/questions/16606364/php-cast-string-to-either-int-or-float
        $this->value = $line + 0;
        return true;
    }

    /**
     * Client
     *
     * @param float|int $fallbackValue
     * @return DelayClient
     */
    public function client($fallbackValue = 0)
    {
        return new DelayClient($this->base, $this->userAgent, $this->value, $fallbackValue);
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        return empty($this->value) ? [] : [$this->directive . ':' . $this->value];
    }
}
