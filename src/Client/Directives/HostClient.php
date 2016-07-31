<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class HostClient
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/HostClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class HostClient extends HostClientCore
{
    /**
     * HostClient constructor.
     * @param string $base
     * @param string $effective
     * @param string[] $host
     */
    public function __construct($base, $effective, $host)
    {
        parent::__construct($base, $effective, $host);
    }

    /**
     * Is preferred host?
     *
     * @return bool
     */
    public function isPreferred()
    {
        if (($host = $this->export()) === null) {
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
     * Export
     *
     * @return string|null
     */
    public function export()
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
        if (($get = $this->export()) !== null) {
            // Host defined by the Host directive
            return $get;
        } elseif ($this->base !== $this->effective &&
            parse_url($this->base, PHP_URL_HOST) === ($host = parse_url($this->effective, PHP_URL_HOST))
        ) {
            // Host is the same, but Scheme or Port is different
            return getservbyname($scheme = parse_url($this->effective, PHP_URL_SCHEME), 'tcp') === parse_url($this->effective, PHP_URL_PORT) ? $scheme . '://' . $host : $this->effective;
        }
        // Return Host name only
        return parse_url($this->effective, PHP_URL_HOST);
    }
}
