<?php
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
    use UriParser;

    /**
     * Paths
     * @var array
     */
    private $paths;

    /**
     * Host
     * @var HostClient
     */
    private $host;

    /**
     * Clean-param
     * @var CleanParamClient
     */
    private $cleanParam;

    /**
     * AllowClient constructor.
     *
     * @param array $paths
     * @param HostClient $host
     * @param CleanParamClient $cleanParam
     */
    public function __construct(array $paths, HostClient $host, CleanParamClient $cleanParam)
    {
        $this->host = $host;
        $this->paths = $paths;
        $this->cleanParam = $cleanParam;
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
            $this->host->isUriListed($uri) ||
            $this->cleanParam->isListed($path)
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
        // Encode
        $uri = mb_split('#', $this->uriEncode($uri))[0];
        if (mb_stripos($uri, '/') === 0) {
            // URI is already an path
            return $uri;
        }
        if (!$this->uriValidate($uri)) {
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
