<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\HostClient;
use vipnytt\RobotsTxtParser\Parser\UriParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class HostParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class HostParser implements ParserInterface, RobotsTxtInterface
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
     * Parent directive
     * @var string|null
     */
    private $parent;

    /**
     * Host values
     * @var string[]
     */
    private $host = [];

    /**
     * Host constructor.
     *
     * @param string $base
     * @param string $effective
     * @param string|null $parentDirective
     */
    public function __construct($base, $effective, $parentDirective = null)
    {
        $this->base = $base;
        $this->effective = $effective;
        $this->parent = $parentDirective;
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
        if (
            $host === false ||
            $line !== $host ||
            in_array($host, $this->host) ||
            (
                $this->parent === null &&
                !empty($this->host)
            )
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
        if (
            !($parts = $this->getParts($line)) ||
            $this->uriValidateIP($parts['host']) ||
            !$this->uriValidateHost($parts['host']) ||
            (
                !empty($parts['scheme']) &&
                !$this->uriValidateScheme($parts['scheme'])
            )
        ) {
            return false;
        }
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
        return ($parsed = parse_url($this->uriEncode(mb_strtolower($uri)))) === false ? false : [
            'scheme' => isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '',
            'host' => isset($parsed['host']) ? $parsed['host'] : $parsed['path'],
            'port' => isset($parsed['port']) ? ':' . $parsed['port'] : '',
        ];
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        $result = [];
        foreach ($this->host as $host) {
            $result[] = self::DIRECTIVE_HOST . ':' . $host;
        }
        sort($result);
        return $result;
    }

    /**
     * Client
     *
     * @return HostClient
     */
    public function client()
    {
        return new HostClient($this->base, $this->effective, $this->host, $this->parent);
    }
}
