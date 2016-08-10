<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\DelayClient;
use vipnytt\RobotsTxtParser\Handler\RenderHandler;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class DelayParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class DelayParser implements ParserInterface, RobotsTxtInterface
{
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
     * Client cache
     * @var DelayClient
     */
    private $client;

    /**
     * DelayParser constructor.
     *
     * @param string $base
     * @param string $directive
     */
    public function __construct($base, $directive)
    {
        $this->base = $base;
        $this->directive = $directive;
    }

    /**
     * Add
     *
     * @param float|int|string $line
     * @return bool
     */
    public function add($line)
    {
        if (!is_numeric($line) ||
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
        if (isset($this->client)) {
            return $this->client;
        }
        return $this->client = new DelayClient($this->base, $userAgent, $this->delay, $fallbackValue);
    }

    /**
     * Render
     *
     * @param RenderHandler $handler
     * @return bool
     */
    public function render(RenderHandler $handler)
    {
        if (!empty($this->delay)) {
            $handler->add($this->directive, $this->delay);
        }
        return true;
    }
}
