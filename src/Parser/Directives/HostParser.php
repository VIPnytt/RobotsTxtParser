<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

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
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_HOST;

    /**
     * Host array
     * @var string[]
     */
    private $array = [];

    /**
     * Host constructor.
     */
    public function __construct()
    {
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
     * Check
     *
     * @param string $url
     * @return bool
     */
    public function check($url)
    {
        if (empty($this->array)) {
            return false;
        }
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
        if (in_array($this->array[0], $cases)) {
            return true;
        }
        return false;
    }

    /**
     * Export rules
     *
     * @return string[][]
     */
    public function export()
    {
        return empty($this->array) ? [] : [self::DIRECTIVE => $this->array];
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
            $result[] = self::DIRECTIVE . ':' . $value;
        }
        return $result;
    }
}
