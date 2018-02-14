<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Parser\UriParser;
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
     * @param  string $uri
     * @return int|false
     */
    public function hasPath($uri)
    {
        return $this->checkPaths($this->getPathFromUri($uri), $this->paths);
    }

    /**
     * Get path and query
     *
     * @param string $uri
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getPathFromUri($uri)
    {
        $uriParser = new UriParser($uri);
        // Prepare uri
        $uriParser->encode();
        $uri = $uriParser->stripFragment();
        if (strpos($uri, '/') === 0) {
            // URI is already an path
            return $uri;
        }
        if (!$uriParser->validate()) {
            throw new \InvalidArgumentException('Invalid URI');
        }
        $path = (($path = parse_url($uri, PHP_URL_PATH)) === null) ? '/' : $path;
        $query = (($query = parse_url($uri, PHP_URL_QUERY)) === null) ? '' : '?' . $query;
        return $path . $query;
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
