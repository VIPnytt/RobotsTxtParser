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
            $this->isHostListed($uri, $this->host->export()) ||
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
     * Is host listed by directive
     *
     * @param string $uri
     * @param string[] $hosts
     * @return bool
     */
    private function isHostListed($uri, $hosts)
    {
        $uriParser = new UriParser($uri);
        $uri = $uriParser->encode();
        $parts = [
            'scheme' => parse_url($uri, PHP_URL_SCHEME),
            'host' => parse_url($uri, PHP_URL_HOST),
        ];
        $parts['port'] = is_int($port = parse_url($uri, PHP_URL_PORT)) ? $port : getservbyname($parts['scheme'], 'tcp');
        $cases = [
            $parts['host'],
            $parts['host'] . ':' . $parts['port'],
            $parts['scheme'] . '://' . $parts['host'],
            $parts['scheme'] . '://' . $parts['host'] . ':' . $parts['port']
        ];
        foreach ($hosts as $host) {
            if (in_array($host, $cases)) {
                return true;
            }
        }
        return false;
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
