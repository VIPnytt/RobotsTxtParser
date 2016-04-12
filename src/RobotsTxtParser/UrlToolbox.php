<?php
namespace vipnytt\RobotsTxtParser;

/**
 * Trait UrlToolbox
 *
 * @package vipnytt\RobotsTxtParser
 */
trait UrlToolbox
{
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
        foreach ($reserved as $replace => $pattern) {
            $url = mb_ereg_replace($pattern, $replace, $url);
        }
        return $url;
    }

    /**
     * Validate URL
     *
     * @param string $url
     * @return bool
     */
    protected function urlValidate($url)
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
    protected static function  urlValidateHost($host)
    {
        return (
            mb_ereg_match('^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$', $host) && //valid chars check
            mb_ereg_match('^.{1,253}$', $host) && //overall length check
            mb_ereg_match('^[^\.]{1,63}(\.[^\.]{1,63})*$', $host) && //length of each label
            !filter_var($host, FILTER_VALIDATE_IP) //is not an IP address
        );
    }

    /**
     * Validate URL scheme
     *
     * @param  string $scheme
     * @return bool
     */
    protected static function urlValidateScheme($scheme)
    {
        return in_array($scheme, [
                'http',
                'https',
                'ftp',
                'sftp',
            ]
        );
    }
}
