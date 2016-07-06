<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Parser\UriParser;

/**
 * Class HostClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class HostClient implements ClientInterface
{
    use UriParser;

    /**
     * Base uri
     * @var string
     */
    private $base;

    /**
     * Effective uri
     * @var string
     */
    private $effective;

    /**
     * Host values
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
     * @param string $effective
     * @param string[] $host
     * @param string|null $parentDirective
     */
    public function __construct($base, $effective, array $host, $parentDirective = null)
    {
        $this->base = $base;
        $this->effective = $effective;
        $this->host = $host;
        $this->parent = $parentDirective;
    }

    /**
     * Is preferred host
     *
     * @return bool
     */
    public function isPreferred()
    {
        if (($host = $this->get()) === null) {
            return $this->base === $this->effective;
        }
        $parsed = parse_url($host);
        $new = [
            'scheme' => isset($parsed['scheme']) ? $parsed['scheme'] : parse_url($this->base, PHP_URL_SCHEME),
            'host' => isset($parsed['host']) ? $parsed['host'] : $parsed['path'],
        ];
        $new['port'] = isset($parsed['port']) ? $parsed['port'] : getservbyname($new['scheme'], 'tcp');
        return $this->base == $new['scheme'] . '://' . $new['host'] . ':' . $new['port'];
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
     * Get with uri redirect fallback
     *
     * @return string
     */
    public function getWithFallback()
    {
        return ($get = $this->get()) === null ? parse_url($this->effective, PHP_URL_HOST) : $get;
    }

    /**
     * Is host listed by directive
     *
     * @param string $uri
     * @return bool
     */
    public function isUriListed($uri)
    {
        $uri = mb_strtolower($this->urlEncode($uri));
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
        foreach ($this->host as $host) {
            if (in_array($host, $cases)) {
                return true;
            }
        }
        return false;
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
