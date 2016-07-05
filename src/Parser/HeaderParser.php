<?php
namespace vipnytt\RobotsTxtParser\Parser;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class HeaderParser
 *
 * @package vipnytt\RobotsTxtParser\Parser
 */
class HeaderParser implements RobotsTxtInterface
{
    /**
     * ANSI C's asctime() format
     */
    const DATE_ASCTIME = 'D M j h:i:s Y';

    /**
     * HTTP date formats
     */
    const DATE_HTTP = [
        DATE_RFC1123,
        DATE_RFC850,
        self::DATE_ASCTIME,
    ];

    /**
     * cURL resource
     * @var resource
     */
    private $ch;

    /**
     * Headers
     * @var string[]
     */
    private $headers;

    /**
     * HeaderParser constructor.
     */
    public function __construct()
    {
    }

    /**
     * cURL CURLOPT_HEADERFUNCTION callback
     *
     * @param resource $ch - cURL resource
     * @param string $headerLine - cURL header line string
     * @return int - the number of bytes written
     */
    public function curlCallback($ch, $headerLine)
    {
        $this->ch = $ch;
        $split = array_map('trim', explode(':', $headerLine, 2));
        $this->headers[strtolower($split[0])] = end($split);
        return strlen($headerLine);
    }

    /**
     * Content-Type encoding HTTP header
     * @link https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.17
     *
     * @return string
     */
    public function getCharset()
    {
        if (
            isset($this->headers['content-type']) &&
            ($value = $this->getInlineValue($this->headers['content-type'], 'charset', ';')) !== false
        ) {
            return $value;
        }
        return self::ENCODING;
    }

    /**
     * Get inline header variable value
     *
     * @param string $header
     * @param string $part
     * @param string $delimiter
     * @return string|false
     */
    private function getInlineValue($header, $part, $delimiter = ";")
    {
        foreach (array_map('trim', explode($delimiter, $header)) as $string) {
            if (stripos($string, $part . '=') === 0) {
                return trim(explode('=', $string, 2)[1]);
            }
        }
        return false;
    }

    /**
     * Cache-Control max-age HTTP header
     * @link https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9.3
     *
     * @return int
     */
    public function getMaxAge()
    {
        if (
            isset($this->headers['cache-control']) &&
            ($value = $this->getInlineValue($this->headers['content-type'], 'max-age', ',')) !== false
        ) {
            return intval($value);
        }
        return 0;
    }

    /**
     * Cache-Control Retry-After HTTP header
     * @link https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.37
     *
     * @param int $requestTime
     * @return int
     */
    public function getRetryAfter($requestTime)
    {
        if (isset($this->headers['retry-after'])) {
            if (is_numeric($this->headers['retry-after'])) {
                return intval($this->headers['retry-after']);
            } elseif (($time = $this->parseHttpDate($this->headers['retry-after'])) !== false) {
                return max(0, $time - $requestTime);
            }
        }
        return 0;
    }

    /**
     * Parse HTTP-date
     * @link https://tools.ietf.org/html/rfc2616#section-3.3
     *
     * @param string $string
     * @return int|false
     */
    private function parseHttpDate($string)
    {
        foreach (self::DATE_HTTP as $format) {
            if (($dateTime = date_create_from_format($format, $string, new \DateTimeZone('GMT'))) !== false) {
                return (int)date_format($dateTime, 'U');
            }
        }
        return false;
    }
}
