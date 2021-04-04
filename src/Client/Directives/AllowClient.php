<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class AllowClient
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/AllowClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class AllowClient implements ClientInterface, RobotsTxtInterface
{
    use DirectiveClientTrait;

    /**
     * Paths
     * @var array
     */
    private $paths;

    /**
     * AllowClient constructor.
     *
     * @param string[] $paths
     */
    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * Check
     *
     * @deprecated 2.1.0
     * @see AllowClient::isCovered()
     *
     * @param  string $uri
     * @return int|false
     */
    public function hasPath($uri)
    {
        return mb_strlen($this->checkPaths($this->getPathFromUri($uri), $this->paths));
    }

    /**
     * Get the most specific rule
     *
     * @param  string $uri
     * @return string|false
     */
    public function isCovered($uri)
    {
        return $this->checkPaths($this->getPathFromUri($uri), $this->paths);
    }

    /**
     * Export
     *
     * @return array
     */
    public function export()
    {
        return $this->paths;
    }
}
