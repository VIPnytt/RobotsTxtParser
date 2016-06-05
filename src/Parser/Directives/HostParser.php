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
    private $array = [];

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
            in_array($host, $this->array)
        ) {
            return false;
        }
        $this->array[] = $line;
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
        if (($parsed = parse_url(($line = $this->urlEncode(mb_strtolower($line))))) === false) {
            return false;
        }
        $line = isset($parsed['host']) ? $parsed['host'] : $parsed['path'];
        if (
            !$this->urlValidateHost($line) ||
            (
                isset($parsed['scheme']) &&
                !$this->urlValidateScheme($parsed['scheme'])
            )
        ) {
            return false;
        }
        $scheme = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        return $scheme . $line . $port;
    }

    /**
     * Client
     *
     * @return HostClient
     */
    public function client()
    {
        return new HostClient($this->base, $this->array, $this->parent);
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        $result = [];
        foreach ($this->array as $value) {
            $result[] = self::DIRECTIVE_HOST . ':' . $value;
        }
        sort($result);
        return $result;
    }
}
