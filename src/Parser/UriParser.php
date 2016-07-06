<?php
namespace vipnytt\RobotsTxtParser\Parser;

use vipnytt\RobotsTxtParser\Exceptions\ClientException;

/**
 * Trait UriParser
 *
 * @package vipnytt\RobotsTxtParser\Parser
 */
trait UriParser
{
    /**
     * Convert relative to full URL
     *
     * @param string $url
     * @param string $base
     * @return string
     * @throws ClientException
     */
    private function urlConvertToFull($url, $base)
    {
        $url = $this->urlEncode($url);
        if ($this->urlValidate($url)) {
            return $url;
        } elseif (stripos($url, '/') === 0) {
            return $this->urlBase($base) . $url;
        }
        throw new ClientException("Invalid URL `$url`");
    }

    /**
     * URL encoder according to RFC 3986
     * Returns a string containing the encoded URL with disallowed characters converted to their percentage encodings.
     * @link http://publicmind.in/blog/url-encoding/
     *
     * @param string $url
     * @return string
     */
    protected function urlEncode($url)
    {
        $reserved = [
            ":" => '!%3A!ui',
            "/" => '!%2F!ui',
            "?" => '!%3F!ui',
            "#" => '!%23!ui',
            "[" => '!%5B!ui',
            "]" => '!%5D!ui',
            "@" => '!%40!ui',
            "!" => '!%21!ui',
            "$" => '!%24!ui',
            "&" => '!%26!ui',
            "'" => '!%27!ui',
            "(" => '!%28!ui',
            ")" => '!%29!ui',
            "*" => '!%2A!ui',
            "+" => '!%2B!ui',
            "," => '!%2C!ui',
            ";" => '!%3B!ui',
            "=" => '!%3D!ui',
            "%" => '!%25!ui'
        ];
        return preg_replace(array_values($reserved), array_keys($reserved), rawurlencode($url));
    }

    /**
     * Validate URL
     *
     * @param string $url
     * @return bool
     */
    private function urlValidate($url)
    {
        return (
            filter_var($url, FILTER_VALIDATE_URL) &&
            ($parsed = parse_url($url)) !== false &&
            $this->urlValidateHost($parsed['host']) &&
            $this->urlValidateScheme($parsed['scheme'])
        );
    }

    /**
     * Validate host name
     *
     * @link http://stackoverflow.com/questions/1755144/how-to-validate-domain-name-in-php
     *
     * @param  string $host
     * @return bool
     */
    private static function urlValidateHost($host)
    {
        return (
            preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $host) //valid chars check
            && preg_match("/^.{1,253}$/", $host) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $host) //length of each label
            && !filter_var($host, FILTER_VALIDATE_IP) //is not an IP address
        );
    }

    /**
     * Validate URL scheme
     *
     * @param  string $scheme
     * @return bool
     */
    private static function urlValidateScheme($scheme)
    {
        return in_array(strtolower($scheme), [
                'http',
                'https',
                'ftp',
                'sftp',
                'ftps',
            ]
        );
    }

    /**
     * Base URL
     *
     * @param string $url
     * @return string
     * @throws ClientException
     */
    protected function urlBase($url)
    {
        if ($this->urlValidate($url) === false) {
            throw new ClientException("Invalid or unsupported URL `$url`");
        }
        $parts = [
            'scheme' => parse_url($url, PHP_URL_SCHEME),
            'host' => parse_url($url, PHP_URL_HOST),
        ];
        $parts['port'] = is_int($port = parse_url($url, PHP_URL_PORT)) ? $port : getservbyname($parts['scheme'], 'tcp');
        return $parts['scheme'] . '://' . $parts['host'] . ':' . $parts['port'];
    }
}
