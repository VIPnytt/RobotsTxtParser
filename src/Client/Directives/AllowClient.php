<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\RobotsTxtParser\Parser\UriParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class AllowClient
 *
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
     * @param  string $url
     * @return bool
     */
    public function isListed($url)
    {
        $path = $this->getPath($url);
        return (
            $this->checkPaths($path, $this->paths) ||
            $this->host->isListed($url) ||
            $this->cleanParam->isListed($path)
        );
    }

    /**
     * Get path and query
     *
     * @param string $url
     * @return string
     * @throws ClientException
     */
    private function getPath($url)
    {
        // Encode
        $url = mb_split('#', $this->urlEncode($url))[0];
        if (mb_stripos($url, '/') === 0) {
            // URL is already an path
            return $url;
        }
        if (!$this->urlValidate($url)) {
            throw new ClientException('Invalid URL');
        }
        $path = (($path = parse_url($url, PHP_URL_PATH)) === null) ? '/' : $path;
        $query = (($query = parse_url($url, PHP_URL_QUERY)) === null) ? '' : '?' . $query;
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
