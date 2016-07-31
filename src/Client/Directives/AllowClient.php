<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Exceptions\ClientException;
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
    use DirectiveClientCommons;

    /**
     * Paths
     * @var array
     */
    private $paths;

    /**
     * Host
     * @var InlineHostClient
     */
    private $host;

    /**
     * Clean-param
     * @var InlineCleanParamClient
     */
    private $cleanParam;

    /**
     * AllowClient constructor.
     *
     * @param array $paths
     * @param InlineHostClient $host
     * @param InlineCleanParamClient $cleanParam
     */
    public function __construct(array $paths, InlineHostClient $host, InlineCleanParamClient $cleanParam)
    {
        $this->host = $host;
        $this->paths = $paths;
        $this->cleanParam = $cleanParam;
    }

    /**
     * Inline Clean-param directive
     *
     * @return InlineCleanParamClient
     */
    public function cleanParam()
    {
        return $this->cleanParam;
    }

    /**
     * Inline Host directive
     *
     * @return InlineHostClient
     */
    public function host()
    {
        return $this->host;
    }

    /**
     * Check
     *
     * @param  string $uri
     * @return bool
     */
    public function isListed($uri)
    {
        $path = $this->getPath($uri);
        return (
            $this->checkPaths($path, $this->paths) ||
            $this->host->isListed($uri) ||
            !empty($this->cleanParam->detect($path))
        );
    }

    /**
     * Get path and query
     *
     * @param string $uri
     * @return string
     * @throws ClientException
     */
    private function getPath($uri)
    {
        $uriParser = new UriParser($uri);
        // Encode
        $uri = explode('#', $uriParser->encode(), 2)[0];
        if (mb_strpos($uri, '/') === 0) {
            // URI is already an path
            return $uri;
        }
        if (!$uriParser->validate()) {
            throw new ClientException('Invalid URI');
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
        return [
            'host' => $this->host->export(),
            'path' => $this->paths,
            'clean-param' => $this->cleanParam->export(),
        ];
    }
}
