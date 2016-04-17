<?php
namespace vipnytt\RobotsTxtParser\Directives;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\UrlToolbox;

/**
 * Class Host
 *
 * @package vipnytt\RobotsTxtParser\Directives
 */
final class Host implements DirectiveInterface, RobotsTxtInterface
{
    use UrlToolbox;

    /**
     * Directive
     */
    const DIRECTIVE = self::DIRECTIVE_HOST;

    /**
     * Host array
     * @var array
     */
    protected $array = [];

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
        $this->array[] = $host;
        return true;
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
     * Export
     *
     * @return array
     */
    public function export()
    {
        return empty($this->array) ? [] : [self::DIRECTIVE => $this->array];
    }

    public function optimize($url)
    {
        //TODO: Change scheme, Host and port
        //$parsed = parse_url($url);

        return $url;
    }
}
