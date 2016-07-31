<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class RobotVersionClient
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/RobotVersionClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class RobotVersionClient implements ClientInterface
{
    /**
     * Robot-version
     * @var string|null
     */
    private $robotVersion;

    /**
     * RobotVersionClient constructor.
     *
     * @param string|null $robotVersion
     */
    public function __construct($robotVersion)
    {
        $this->robotVersion = $robotVersion;
    }

    /**
     * Export
     *
     * @return string|null
     */
    public function export()
    {
        return $this->robotVersion;
    }
}
