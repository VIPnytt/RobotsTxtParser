<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

class DisAllowClient implements ClientInterface, RobotsTxtInterface
{
    use DirectiveClientCommons;
    use UrlParser;

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
     * DisAllowClient constructor.
     *
     * @param array $paths
     * @param HostClient $host
     * @param CleanParamClient $cleanParam
     */
    public function __construct(array $paths, HostClient $host, CleanParamClient $cleanParam)
    {
        $this->paths = $paths;
        $this->host = $host;
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
            $this->cleanParam->isListed($path) ||
            $this->host->isListed($url)
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
            // URL already an path
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
