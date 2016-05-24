<?php
namespace vipnytt\RobotsTxtParser;

use GuzzleHttp;
use vipnytt\RobotsTxtParser\Client;

/**
 * Class Request
 *
 * @package vipnytt\RobotsTxtParser
 */
class Request extends Client
{
    const GUZZLE_HTTP_CONFIG = [
        'allow_redirects' => [
            'max' => self::MAX_REDIRECTS,
            'referer' => true,
            'strict' => true,
        ],
        'decode_content' => true,
        'headers' => [
            'Accept' => 'text/plain;q=1.0, text/*;q=0.8, */*;q=0.1',
            'Accept-Charset' => 'utf-8;q=1.0, *;q=0.1',
            'Accept-Encoding' => 'identity;q=1.0, *;q=0.1',
            'User-Agent' => 'RobotsTxtParser-VIPnytt/2.0 (+https://github.com/VIPnytt/RobotsTxtParser/blob/master/README.md)',
        ],
        'http_errors' => false,
        'timeout' => 60,
        'verify' => true,
    ];

    /**
     * Request timestamp
     * @var int
     */
    protected $time;

    /**
     * Cache-Control max-age
     * @var int
     */
    protected $maxAge;

    /**
     * HTTP Status code
     * @var int
     */
    protected $statusCode;

    /**
     * Robots.txt contents
     * @var string
     */
    protected $contents;

    /**
     * Robots.txt character encoding
     * @var string
     */
    protected $encoding;

    /**
     * Request constructor.
     *
     * @param string $baseUri
     * @param array $guzzleConfig
     * @param int|null $byteLimit
     */
    public function __construct($baseUri, array $guzzleConfig = [], $byteLimit = self::BYTE_LIMIT)
    {
        $baseUri = $this->urlBase($this->urlEncode($baseUri));
        try {
            $client = new GuzzleHttp\Client(
                array_merge_recursive(
                    self::GUZZLE_HTTP_CONFIG,
                    $guzzleConfig,
                    [
                        'base_uri' => $baseUri,
                    ]
                )
            );
            $response = $client->request('GET', self::PATH);
            $this->time = time();
            $this->statusCode = $response->getStatusCode();
            $this->contents = $response->getBody()->getContents();
            $this->encoding = $this->headerEncoding($response->getHeader('content-type'));
            $this->maxAge = $this->headerMaxAge($response->getHeader('cache-control'));
        } catch (GuzzleHttp\Exception\TransferException $e) {
            $this->statusCode = 523;
            $this->contents = '';
            $this->encoding = self::ENCODING;
            $this->maxAge = 0;
        }
        parent::__construct($baseUri, $this->statusCode, $this->contents, $this->encoding, $byteLimit);
    }

    /**
     * Content-Type encoding HTTP header
     *
     * @param array $headers
     * @return string
     */
    protected function headerEncoding(array $headers)
    {
        if (($value = $this->parseHeader($headers, 'charset', ';')) !== false) {
            return $value;
        }
        return self::ENCODING;
    }

    /**
     * Client header
     *
     * @param array $headers
     * @param string $part
     * @param string $delimiter
     * @return string|false
     */
    protected function parseHeader(array $headers, $part, $delimiter = ";")
    {
        foreach ($headers as $header) {
            $split = array_map('trim', mb_split($delimiter, $header));
            foreach ($split as $string) {
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
     * @param array $headers
     * @return int
     */
    protected function headerMaxAge(array $headers)
    {
        if (($value = $this->parseHeader($headers, 'max-age', ',')) !== false) {
            return intval($value);
        }
        return 0;
    }

    /**
     * Base URI
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
     * @return int
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
        return $this->time + self::CACHE_TIME;
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
