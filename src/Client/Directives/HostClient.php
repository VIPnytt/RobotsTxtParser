<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Parser\UrlParser;

/**
 * Class HostClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class HostClient implements ClientInterface
{
    use UrlParser;

    /**
     * Base Uri
     * @var string
     */
    private $base;

    /**
     * Host
     * @var string[]
     */
    private $host;

    /**
     * Parent directive
     * @var string|null
     */
    private $parent;

    /**
     * HostClient constructor.
     *
     * @param string $base
     * @param string[] $host
     * @param string|null $parentDirective
     */
    public function __construct($base, array $host, $parentDirective = null)
    {
        $this->base = $base;
        $this->host = $host;
        $this->parent = $parentDirective;
    }

    /**
     * Check
     *
     * @param string $url
     * @return bool
     */
    public function isListed($url)
    {
        $url = mb_strtolower($this->urlEncode($url));
        $parts = [
            'scheme' => parse_url($url, PHP_URL_SCHEME),
            'host' => parse_url($url, PHP_URL_HOST),
        ];
        $parts['port'] = is_int($port = parse_url($url, PHP_URL_PORT)) ? $port : getservbyname($parts['scheme'], 'tcp');
        $cases = [
            $parts['host'],
            $parts['host'] . ':' . $parts['port'],
            $parts['scheme'] . '://' . $parts['host'],
            $parts['scheme'] . '://' . $parts['host'] . ':' . $parts['port']
        ];
        foreach ($this->host as $host) {
            if (in_array($host, $cases)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Preferred host
     *
     * @return bool
     */
    public function isPreferred()
    {
        return empty($this->host) ? true : mb_stripos($this->urlBase($this->urlEncode($this->base)), $this->get()) !== false;
    }

    /**
     * Get
     *
     * @return string|null
     */
    public function get()
    {
        return isset($this->host[0]) ? $this->host[0] : null;
    }

    /**
     * Export
     *
     * @return string[]|string|null
     */
    public function export()
    {
        if ($this->parent === null) {
            return isset($this->host[0]) ? $this->host[0] : null;
        }
        return $this->host;
    }
}
