<?php
namespace vipnytt\RobotsTxtParser\Directives;

use vipnytt\RobotsTxtParser\UrlToolbox;

/**
 * Class Host
 *
 * @package vipnytt\RobotsTxtParser\Directives
 */
class Host implements DirectiveInterface
{
    use UrlToolbox;

    /**
     * Directive
     */
    const DIRECTIVE = 'Host';

    protected $array = [];
    protected $parent;


    public function __construct($parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
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

        $host = $scheme . $line . $port;
        if (in_array($host, $this->array)) {
            return false;
        }
        if ($this->parent !== null) {
            $this->array[] = $host;
            return true;
        }
        if (!empty($this->array)) {
            return false;
        }
        $this->array = [$host];
        return true;
    }

    /**
     * Check Host rule
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

    public function export()
    {
        return empty($this->array) ? [] : [self::DIRECTIVE => $this->array];
    }
}
