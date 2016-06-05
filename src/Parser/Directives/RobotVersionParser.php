<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\RobotVersionClient;
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
    private $robotVersion;

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
        if (!empty($this->robotVersion)) {
            return false;
        }
        $this->robotVersion = $line;
        return true;
    }

    /**
     * Client
     *
     * @return RobotVersionClient
     */
    public function client()
    {
        return new RobotVersionClient($this->robotVersion);
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        return empty($this->robotVersion) ? [] : [self::DIRECTIVE_ROBOT_VERSION . ':' . $this->robotVersion];
    }
}
