<?php
namespace vipnytt\RobotsTxtParser;

use GuzzleHttp;

/**
 * Class UriClient
 *
 * @package vipnytt\RobotsTxtParser
 */
class UriClient extends TxtClient
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
     * GuzzleHttp config
     */
    const GUZZLE_HTTP_CONFIG = [
        'allow_redirects' => [
            'max' => self::MAX_REDIRECTS,
            'referer' => true,
            'strict' => true,
        ],
        'decode_content' => false,
        'headers' => [
            'accept' => 'text/plain;q=1.0, text/*;q=0.8, */*;q=0.1',
            'accept-charset' => 'utf-8;q=1.0, *;q=0.1',
            'accept-encoding' => 'identity;q=1.0, *;q=0.1',
            'user-agent' => 'RobotsTxtParser-VIPnytt/2.0 (+https://github.com/VIPnytt/RobotsTxtParser/blob/master/README.md)',
        ],
        'http_errors' => false,
        'verify' => true,
    ];

    /**
     * Base uri
     * @var string
     */
    private $base;

    /**
     * RequestClient timestamp
     * @var int
     */
    private $time;

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    private $response;

    /**
     * Cache-Control max-age
     * @var int
     */
    private $maxAge;

    /**
     * Robots.txt contents
     * @var string
     */
    private $contents;

    /**
     * Robots.txt character encoding
     * @var string
     */
    private $encoding;

    /**
     * RequestClient constructor.
     *
     * @param string $baseUri
     * @param array $guzzleConfig
     * @param int|null $byteLimit
     */
    public function __construct($baseUri, array $guzzleConfig = [], $byteLimit = self::BYTE_LIMIT)
    {
        $this->base = $this->urlBase($this->urlEncode($baseUri));
        $this->time = time();
        try {
            $client = new GuzzleHttp\Client(
                array_merge_recursive(
                    self::GUZZLE_HTTP_CONFIG,
                    $guzzleConfig,
                    [
                        'base_uri' => $this->base,
                    ]
                )
            );
            $this->response = $client->request('GET', self::PATH);
            $this->time = time();
            $this->statusCode = $this->response->getStatusCode();
            $this->contents = $this->response->getBody()->getContents();
            $this->encoding = $this->headerCharset();
            $this->maxAge = $this->headerMaxAge();
        } catch (GuzzleHttp\Exception\TransferException $e) {
            $this->statusCode = null;
            $this->contents = '';
            $this->encoding = self::ENCODING;
            $this->maxAge = 0;
        }
        parent::__construct($this->base, $this->statusCode, $this->contents, $this->encoding, $byteLimit);
    }

    /**
     * Content-Type encoding HTTP header
     *
     * @link https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.17
     *
     * @return string
     */
    private function headerCharset()
    {
        if (($value = $this->parseHeader($this->response->getHeader('content-type'), 'charset', ';')) !== false) {
            return $value;
        }
        return self::ENCODING;
    }

    /**
     * Client header
     *
     * @param string[] $headers
     * @param string $part
     * @param string $delimiter
     * @return string|false
     */
    private function parseHeader(array $headers, $part, $delimiter = ";")
    {
        foreach ($headers as $header) {
            foreach (array_map('trim', mb_split($delimiter, $header)) as $string) {
                if (mb_stripos($string, $part . '=') === 0) {
                    return mb_split('=', $string, 2)[1];
                }
            }
        }
        return false;
    }

    /**
     * Cache-Control max-age HTTP header
     *
     * @link https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9.3
     *
     * @return int
     */
    private function headerMaxAge()
    {
        if (($value = $this->parseHeader($this->response->getHeader('cache-control'), 'max-age', ',')) !== false) {
            return intval($value);
        }
        return 0;
    }

    /**
     * Base UriClient
     *
     * @return string
     */
    public function getBaseUri()
    {
        return $this->base;
    }

    /**
     * Status code
     *
     * @return int|null
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * URL content
     *
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Next update timestamp
     *
     * @return int
     */
    public function nextUpdate()
    {
        if (
            $this->statusCode === 503 &&
            ($retryTime = $this->headerRetryAfter()) !== false
        ) {
            return min($this->time + self::CACHE_TIME, $retryTime);
        }
        return $this->time + self::CACHE_TIME;
    }

    /**
     * Cache-Control Retry-After HTTP header
     *
     * @link https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.37
     *
     * @return int|false
     */
    private function headerRetryAfter()
    {
        foreach ($this->response->getHeader('retry-after') as $parts) {
            $value = implode(', ', $parts);
            if (is_numeric($value)) {
                return $this->time + $value;
            } elseif (($time = $this->parseHttpDate($value)) !== false) {
                return $time;
            }
        }
        return false;
    }

    /**
     * Parse HTTP-date
     *
     * @param string $string
     * @return int|false
     */
    private function parseHttpDate($string)
    {
        foreach (self::DATE_HTTP as $format) {
            $dateTime = date_create_from_format($format, $string, new \DateTimeZone('GMT'));
            if ($dateTime !== false) {
                return (int)date_format($dateTime, 'U');
            }
        }
        return false;
    }

    /**
     * Valid until timestamp
     *
     * @return int
     */
    public function validUntil()
    {
        return $this->time + max(self::CACHE_TIME, $this->maxAge);
    }
}
