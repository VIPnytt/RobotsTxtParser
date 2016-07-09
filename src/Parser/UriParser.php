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
     * Convert relative to full uri
     *
     * @param string $uri
     * @param string $base
     * @return string
     * @throws ClientException
     */
    private function uriConvertToFull($uri, $base)
    {
        $uri = $this->uriEncode($uri);
        if ($this->uriValidate($uri)) {
            return $uri;
        } elseif (stripos($uri, '/') === 0) {
            return $this->uriBase($base) . $uri;
        }
        throw new ClientException("Invalid URI `$uri`");
    }

    /**
     * URI encoder according to RFC 3986
     * Returns a string containing the encoded URI with disallowed characters converted to their percentage encodings.
     * @link http://publicmind.in/blog/url-encoding/
     *
     * @param string $uri
     * @return string
     */
    protected function uriEncode($uri)
    {
        $reserved = [
            '!%21!ui' => "!",
            '!%23!ui' => "#",
            '!%24!ui' => "$",
            '!%25!ui' => "%",
            '!%26!ui' => "&",
            '!%27!ui' => "'",
            '!%28!ui' => "(",
            '!%29!ui' => ")",
            '!%2A!ui' => "*",
            '!%2B!ui' => "+",
            '!%2C!ui' => ",",
            '!%2F!ui' => "/",
            '!%3A!ui' => ":",
            '!%3B!ui' => ";",
            '!%3D!ui' => "=",
            '!%3F!ui' => "?",
            '!%40!ui' => "@",
            '!%5B!ui' => "[",
            '!%5D!ui' => "]",
        ];
        return preg_replace(array_keys($reserved), array_values($reserved), rawurlencode($uri));
    }

    /**
     * Validate uri
     *
     * @param string $uri
     * @return bool
     */
    private function uriValidate($uri)
    {
        return (
            (
                filter_var($uri, FILTER_VALIDATE_URL) ||
                // PHP 5.x bug fix: FILTER_VALIDATE_URL doesn't support IPv6 urls. IP check not needed in the future.
                $this->uriValidateIP(($parsed = parse_url($uri, PHP_URL_HOST)) === false ? '' : $parsed)
            ) &&
            ($parsed = parse_url($uri)) !== false &&
            (
                $this->uriValidateHost($parsed['host']) ||
                $this->uriValidateIP($parsed['host'])
            ) &&
            $this->uriValidateScheme($parsed['scheme'])
        );
    }

    /**
     * Validate IPv4 or IPv6
     *
     * @param  string $ipAddress
     * @return bool
     */
    private function uriValidateIP($ipAddress)
    {
        return (
            filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ||
            filter_var(trim($ipAddress, '[]'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
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
    private function uriValidateHost($host)
    {
        return (
            preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $host) //valid chars check
            && preg_match("/^.{1,253}$/", $host) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $host) //length of each label
            && !$this->uriValidateIP($host)
        );
    }

    /**
     * Validate uri scheme
     *
     * @param  string $scheme
     * @return bool
     */
    private function uriValidateScheme($scheme)
    {
        return in_array(strtolower($scheme), [
                'http',
                'https',
                'ftp',
                'ftps',
                'sftp',
            ]
        );
    }

    /**
     * Base uri
     *
     * @param string $uri
     * @return string
     * @throws ClientException
     */
    protected function uriBase($uri)
    {
        if ($this->uriValidate($uri) === false) {
            throw new ClientException("Invalid or unsupported URI `$uri`");
        }
        $parts = [
            'scheme' => parse_url($uri, PHP_URL_SCHEME),
            'host' => parse_url($uri, PHP_URL_HOST),
        ];
        $parts['port'] = is_int($port = parse_url($uri, PHP_URL_PORT)) ? $port : getservbyname($parts['scheme'], 'tcp');
        return $parts['scheme'] . '://' . $parts['host'] . ':' . $parts['port'];
    }
}
