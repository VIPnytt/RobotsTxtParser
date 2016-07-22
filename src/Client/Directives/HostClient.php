<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class HostClient
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/HostClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class HostClient implements ClientInterface
{
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
     * Get Host
     *
     * @return string|null
     */
    public function get()
    {
        return isset($this->host[0]) ? $this->host[0] : null;
    }

    /**
     * Get Host, falls back to Effective Request URI if not found
     *
     * @return string
     */
    public function getWithUriFallback()
    {
        if (($get = $this->get()) !== null) {
            // Host defined by the Host directive
            return $get;
        } elseif (
            $this->base !== $this->effective &&
            parse_url($this->base, PHP_URL_HOST) === ($host = parse_url($this->effective, PHP_URL_HOST))
        ) {
            // Host is the same, but Scheme or Port is different
            return getservbyname($scheme = parse_url($this->effective, PHP_URL_SCHEME), 'tcp') === parse_url($this->effective, PHP_URL_PORT) ? $scheme . '://' . $host : $this->effective;
        }
        // Return Host name only
        return parse_url($this->effective, PHP_URL_HOST);
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
