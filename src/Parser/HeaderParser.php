<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Parser;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class HeaderParser
 *
 * @link https://tools.ietf.org/html/rfc7231
 * @link https://tools.ietf.org/html/rfc7230
 * @link https://tools.ietf.org/html/rfc2616
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
     * @link https://tools.ietf.org/html/rfc7231#section-7.1.1
     * @link https://tools.ietf.org/html/rfc2616#section-3.3
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
    protected $curlHandler;

    /**
     * Headers
     * @var string[]
     */
    private $headers;

    /**
     * HeaderParser constructor.
     *
     * @param resource $handler
     */
    public function __construct($handler)
    {
        $this->curlHandler = $handler;
    }

    /**
     * cURL CURLOPT_HEADERFUNCTION callback
     * @link https://tools.ietf.org/html/rfc7230#section-3.2.4
     *
     * This callback function must return the number of bytes actually taken care of.
     * If that amount differs from the amount passed in to your function, it'll signal an error to the library.
     * This will cause the transfer to get aborted and the libcurl function in progress will return CURLE_WRITE_ERROR.
     * @link https://curl.haxx.se/libcurl/c/CURLOPT_HEADERFUNCTION.html
     *
     * @param resource $handler - cURL resource
     * @param string $line - cURL header line string
     * @return int - the number of bytes written
     */
    public function curlCallback($handler, $line)
    {
        $this->curlHandler = $handler;
        $split = explode(':', $line, 2);
        $this->headers[strtolower($split[0])] = trim(end($split));
        return strlen($line);
    }

    /**
     * Content-Type encoding HTTP header
     * @link https://tools.ietf.org/html/rfc2616#section-14.17
     *
     * @return string
     */
    public function getCharset()
    {
        if (isset($this->headers['content-type']) &&
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
     * @link https://tools.ietf.org/html/rfc7234#section-5.2.1.1
     * @link https://tools.ietf.org/html/rfc7234#section-5.2.2.8
     * @link https://tools.ietf.org/html/rfc2616#section-14.9.3
     *
     * @return int
     */
    public function getMaxAge()
    {
        if (isset($this->headers['cache-control']) &&
            ($value = $this->getInlineValue($this->headers['cache-control'], 'max-age', ',')) !== false
        ) {
            return intval($value);
        }
        return 0;
    }

    /**
     * Cache-Control Retry-After HTTP header
     * @link https://tools.ietf.org/html/rfc2616#section-14.37
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
        // If no valid Retry-after header is found, retry after 15 minutes
        return 900;
    }

    /**
     * Parse HTTP-date
     * @link https://tools.ietf.org/html/rfc7231#section-7.1.1
     * @link https://tools.ietf.org/html/rfc2616#section-3.3
     *
     * @param string $string
     * @return int|false
     */
    private function parseHttpDate($string)
    {
        foreach (self::DATE_HTTP as $format) {
            if (($dateTime = date_create_from_format($format, $string, new \DateTimeZone('UTC'))) !== false) {
                return (int)date_format($dateTime, 'U');
            }
        }
        return false;
    }
}
