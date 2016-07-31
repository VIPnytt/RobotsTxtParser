<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Parser\UriParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class HostParser
 *
 * @link http://tools.ietf.org/html/rfc952
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
abstract class HostParserCore implements ParserInterface, RobotsTxtInterface
{
    /**
     * Base uri
     * @var string
     */
    protected $base;

    /**
     * Effective uri
     * @var string
     */
    protected $effective;

    /**
     * Host values
     * @var string[]
     */
    protected $host = [];

    /**
     * HostParser constructor.
     *
     * @param string $base
     * @param string $effective
     */
    public function __construct($base, $effective)
    {
        $this->base = $base;
        $this->effective = $effective;
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        $host = $this->parse($line);
        if ($host === false ||
            $host !== $line ||
            in_array($host, $this->host)
        ) {
            return false;
        }
        $this->host[] = $line;
        return true;
    }

    /**
     * Parse
     *
     * @param string $line
     * @return string|false
     */
    private function parse($line)
    {
        $uriParser = new UriParser($line);
        $line = $uriParser->encode();
        if ($uriParser->validateIP() ||
            !$uriParser->validateHost() ||
            (
                parse_url($line, PHP_URL_SCHEME) !== null &&
                !$uriParser->validateScheme()
            )
        ) {
            return false;
        }
        $parts = $this->getParts($line);
        return $parts['scheme'] . $parts['host'] . $parts['port'];
    }

    /**
     * Get URI parts
     *
     * @param string $uri
     * @return string[]|false
     */
    private function getParts($uri)
    {
        return ($parsed = parse_url($uri)) === false ? false : [
            'scheme' => isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '',
            'host' => isset($parsed['host']) ? $parsed['host'] : $parsed['path'],
            'port' => isset($parsed['port']) ? ':' . $parsed['port'] : '',
        ];
    }
}
