<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\HostClient;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class HostParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class HostParser implements ParserInterface, RobotsTxtInterface
{
    use UrlParser;

    /**
     * Base Uri
     * @var string
     */
    private $base;

    /**
     * Parent directive
     * @var string|null
     */
    private $parent;

    /**
     * Host array
     * @var string[]
     */
    private $host = [];

    /**
     * Host constructor.
     *
     * @param string $base
     * @param string|null $parentDirective
     */
    public function __construct($base, $parentDirective = null)
    {
        $this->base = $base;
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
            in_array($host, $this->host)
        ) {
            return false;
        }
        $this->host[] = $line;
        return true;
    }

    /**
     * Client
     *
     * @param string $line
     * @return string|false
     */
    private function parse($line)
    {
        if (
            !($parts = $this->getParts($line)) ||
            !$this->urlValidateHost($parts['host']) ||
            (
                !empty($parts['scheme']) &&
                !$this->urlValidateScheme($parts['scheme'])
            )
        ) {
            return false;
        }
        return $parts['scheme'] . $parts['host'] . $parts['port'];
    }

    /**
     * Get URL parts
     *
     * @param string $url
     * @return string[]|false
     */
    private function getParts($url)
    {
        return ($parsed = parse_url($this->urlEncode(mb_strtolower($url)))) === false ? false : [
            'scheme' => isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '',
            'host' => isset($parsed['host']) ? $parsed['host'] : $parsed['path'],
            'port' => isset($parsed['port']) ? ':' . $parsed['port'] : '',
        ];
    }

    /**
     * Client
     *
     * @return HostClient
     */
    public function client()
    {
        return new HostClient($this->base, $this->host, $this->parent);
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
}
