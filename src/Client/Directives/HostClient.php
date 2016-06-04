<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;
use vipnytt\RobotsTxtParser\Parser\UrlParser;

/**
 * Class HostClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class HostClient
{
    use UrlParser;

    /**
     * Base Uri
     * @var string
     */
    private $base;

    /**
     * Host
     * @var array
     */
    private $host;

    /**
     * HostClient constructor.
     *
     * @param string $base
     * @param array $host
     */
    public function __construct($base, array $host)
    {
        $this->base = $base;
        $this->host = $host;
    }

    /**
     * Check
     *
     * @param string $url
     * @return bool
     */
    public function check($url)
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
        if (in_array($this->get(), $cases)) {
            return true;
        }
        return false;
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
     * Preferred host
     *
     * @return bool
     */
    public function isMainHost()
    {
        return empty($this->host) ? true : mb_stripos($this->urlBase($this->urlEncode($this->base)), $this->get()) !== false;
    }

    /**
     * Export
     *
     * @return array
     */
    public function export()
    {
        return $this->host;
    }
}
