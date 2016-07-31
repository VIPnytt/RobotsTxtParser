<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Directives;

use vipnytt\RobotsTxtParser\Parser\UriParser;

/**
 * Class InlineHostClient
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/InlineHostClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class InlineHostClient extends HostClientCore
{
    /**
     * InlineHostClient constructor.
     *
     * @param string $base
     * @param string $effective
     * @param string[] $host
     */
    public function __construct($base, $effective, $host)
    {
        parent::__construct($base, $effective, $host);
    }

    /**
     * Export
     *
     * @return string[]
     */
    public function export()
    {
        return $this->host;
    }

    /**
     * Is listed?
     *
     * @param string $uri
     * @return bool
     */
    public function isListed($uri)
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
        foreach ($this->host as $host) {
            if (in_array($host, $cases)) {
                return true;
            }
        }
        return false;
    }
}
