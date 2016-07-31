<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\RobotVersionClient;
use vipnytt\RobotsTxtParser\Handler\RenderHandler;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class RobotVersionParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class RobotVersionParser implements ParserInterface, RobotsTxtInterface
{
    /**
     * RobotVersion value
     * @var float|int|string
     */
    private $version;

    /**
     * RobotVersionParser constructor.
     */
    public function __construct()
    {
    }

    /**
     * Add
     *
     * @param float|int|string $line
     * @return bool
     */
    public function add($line)
    {
        if (!empty($this->version)) {
            return false;
        }
        $this->version = $line;
        return true;
    }

    /**
     * Client
     *
     * @return RobotVersionClient
     */
    public function client()
    {
        return new RobotVersionClient($this->version);
    }

    /**
     * Render
     *
     * @param RenderHandler $handler
     * @return bool
     */
    public function render(RenderHandler $handler)
    {
        if (!empty($this->version)) {
            $handler->add(self::DIRECTIVE_ROBOT_VERSION, $this->version);
        }
        return true;
    }
}
