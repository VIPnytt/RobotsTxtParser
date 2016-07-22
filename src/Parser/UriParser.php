<?php
namespace vipnytt\RobotsTxtParser\Parser;

use vipnytt\RobotsTxtParser\Exceptions\ClientException;

class UriParser
{
    /**
     * URI
     * @var string
     */
    private $uri;

    /**
     * UriParser constructor.
     *
     * @param $uri
     */
    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Convert relative to full
     *
     * @param string $fallbackBase
     * @return string
     * @throws ClientException
     */
    public function convertToFull($fallbackBase)
    {
        $this->encode();
        if ($this->validate()) {
            return $this->uri;
        } elseif (strpos($this->uri, '/') === 0) {
            $relative = $this->uri;
            $this->uri = $fallbackBase;
            return $this->base() . $relative;
        }
        throw new ClientException("Invalid URI `$this->uri`");
    }

    /**
     * URI encoder according to RFC 3986
     * Returns a string containing the encoded URI with disallowed characters converted to their percentage encodings.
     * @link http://publicmind.in/blog/url-encoding/
     *
     * @return string
     */
    public function encode()
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
        $this->uri = preg_replace(array_keys($reserved), array_values($reserved), rawurlencode($this->uri));
        return $this->baseToLowercase();
    }

    /**
     * Base uri to lowercase
     *
     * @return string
     */
    private function baseToLowercase()
    {
        if (($host = parse_url($this->uri, PHP_URL_HOST)) === null) {
            return $this->uri;
        }
        $pos = strpos($this->uri, $host) + strlen($host);
        return $this->uri = substr_replace($this->uri, strtolower(substr($this->uri, 0, $pos)), 0, $pos);
    }

    /**
     * Validate
     *
     * @return bool
     */
    public function validate()
    {
        return (
            (
                filter_var($this->uri, FILTER_VALIDATE_URL) ||
                // PHP 5.x bug fix: FILTER_VALIDATE_URL doesn't support IPv6 urls. IP check not needed in the future.
                $this->validateIP(($parsed = parse_url($this->uri, PHP_URL_HOST)) === false ? '' : $parsed)
            ) &&
            ($parsed = parse_url($this->uri)) !== false &&
            (
                $this->validateHost($parsed['host']) ||
                $this->validateIP($parsed['host'])
            ) &&
            $this->validateScheme($parsed['scheme'])
        );
    }

    /**
     * Validate IPv4 or IPv6
     *
     * @param  string|null $ipAddress
     * @return bool
     */
    public function validateIP($ipAddress = null)
    {
        if ($ipAddress === null) {
            $parsed = parse_url($this->uri);
            $ipAddress = isset($parsed['host']) ? $parsed['host'] : $parsed['path'];
        }
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
     * @param  string|null $host
     * @return bool
     */
    public function validateHost($host = null)
    {
        if ($host === null) {
            $parsed = parse_url($this->uri);
            $host = isset($parsed['host']) ? $parsed['host'] : $parsed['path'];
        }
        return (
            preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $host) //valid chars check
            && preg_match("/^.{1,253}$/", $host) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $host) //length of each label
            && !$this->validateIP($host)
        );
    }

    /**
     * Validate scheme
     *
     * @param  string|null $scheme
     * @return bool
     */
    public function validateScheme($scheme = null)
    {
        if ($scheme === null) {
            $parsed = parse_url($this->uri);
            $scheme = isset($parsed['host']) ? $parsed['host'] : $parsed['path'];
        }
        return in_array($scheme, [
                'http',
                'https',
                'ftp',
                'ftps',
                'sftp',
            ]
        );
    }

    /**
     * Base
     *
     * @return string
     * @throws ClientException
     */
    public function base()
    {
        if (!$this->validate()) {
            throw new ClientException("Invalid URI: $this->uri");
        }
        $parts = [
            'scheme' => parse_url($this->uri, PHP_URL_SCHEME),
            'host' => parse_url($this->uri, PHP_URL_HOST),
        ];
        $parts['port'] = is_int($port = parse_url($this->uri, PHP_URL_PORT)) ? $port : getservbyname($parts['scheme'], 'tcp');
        return strtolower($parts['scheme'] . '://' . $parts['host'] . ':' . $parts['port']);
    }
}
