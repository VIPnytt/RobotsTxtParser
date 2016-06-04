<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\RobotsTxtParser\Parser\Directives\DirectiveParserCommons;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

class DisAllowClient implements RobotsTxtInterface
{
    use DirectiveParserCommons;
    use UrlParser;

    /**
     * Paths
     * @var array
     */
    private $array;

    /**
     * Clean-param
     * @var CleanParamClient
     */
    private $cleanParam;

    /**
     * Host
     * @var HostClient
     */
    private $host;

    /**
     * DisAllowClient constructor.
     *
     * @param array $paths
     * @param CleanParamClient $cleanParam
     * @param HostClient $host
     */
    public function __construct(array $paths, CleanParamClient $cleanParam, HostClient $host)
    {
        $this->array = $paths;
        $this->cleanParam = $cleanParam;
        $this->host = $host;
    }

    /**
     * Check
     *
     * @param  string $url
     * @return bool
     */
    public function affected($url)
    {
        $path = $this->getPath($url);
        return (
            $this->checkPath($path, isset($this->array['path']) ? $this->array['path'] : []) ||
            $this->cleanParam->check($path) ||
            $this->host->check($url)
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
        $url = $this->urlEncode($url);
        if (mb_stripos($url, '/') === 0) {
            // Strip fragments
            $url = mb_split('#', $url)[0];
            return $url;
        }
        if (!$this->urlValidate($url)) {
            throw new ClientException('Invalid URL');
        }
        $path = (($path = parse_url($url, PHP_URL_PATH)) === null) ? '/' : $path;
        $query = (($query = parse_url($url, PHP_URL_QUERY)) === null) ? '' : '?' . $query;
        return $path . $query;
    }
}
