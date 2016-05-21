<?php
namespace vipnytt\RobotsTxtParser;

use DateTime;
use GuzzleHttp;
use vipnytt\RobotsTxtParser\Client;
use vipnytt\RobotsTxtParser\Parser\RobotsTxtInterface;

/**
 * Class Download
 *
 * @package vipnytt\RobotsTxtParser
 */
class Download implements RobotsTxtInterface
{
    /**
     * Base uri
     * @var string
     */
    protected $baseUri;

    /**
     * Download timestamp
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
     * Parser client class
     * @var Client
     */
    protected $parserClient;

    /**
     * Download constructor.
     *
     * @param string $baseUri
     * @param array $guzzleConfig
     */
    public function __construct($baseUri, array $guzzleConfig = [])
    {
        $this->baseUri = $baseUri;
        try {
            $client = new GuzzleHttp\Client(
                array_merge_recursive(
                    [
                        'allow_redirects' => [
                            'max' => self::MAX_REDIRECTS,
                            'referer' => true,
                            'strict' => true,
                        ],
                        'base_uri' => $baseUri,
                        'decode_content' => true,
                        'headers' => [
                            'Accept' => 'text/plain;q=1.0, text/*;q=0.8, */*;q=0.1',
                            'Accept-Charset' => 'utf-8;q=1.0, *;q=0.1',
                            'Accept-Encoding' => 'identity;q=1.0, *;q=0.1',
                            'User-Agent' => 'RobotsTxtParser-VIPnytt/1.0 (+https://github.com/VIPnytt/RobotsTxtParser/blob/master/README.md)',
                        ],
                        'http_errors' => false,
                        'timeout' => 60,
                        'verify' => true,
                    ],
                    $guzzleConfig
                )
            );
            $response = $client->request('GET', '/robots.txt');
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
    }

    /**
     * Content-Type encoding HTTP header
     *
     * @param array $headers
     * @return string
     */
    protected function headerEncoding(array $headers)
    {
        foreach ($headers as $header) {
            $split = array_map('trim', mb_split(';', $header));
            foreach ($split as $string) {
                if (mb_stripos($string, 'charset=') === 0) {
                    return mb_split('=', $string, 2)[1];
                }
            }
        }
        return self::ENCODING;
    }

    /**
     * Cache-Control max-age HTTP header
     *
     * @param array $headers
     * @return int
     */
    protected function headerMaxAge(array $headers)
    {
        foreach ($headers as $header) {
            $split = array_map('trim', mb_split(',', $header));
            foreach ($split as $string) {
                if (mb_stripos($string, 'max-age=') === 0) {
                    return intval(mb_split('=', $string, 2)[1]);
                }
            }
        }
        return 0;
    }

    /**
     * Parser client
     *
     * @param int|null $byteLimit
     * @return Client
     */
    public function parserClient($byteLimit = self::BYTE_LIMIT)
    {
        if (!is_object($this->parserClient)) {
            $this->parserClient = new Client($this->baseUri, $this->getStatusCode(), $this->getContents(), $this->getEncoding(), $byteLimit);
        }
        return $this->parserClient;
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
     * @return \DateTime|false
     */
    public function nextUpdate()
    {
        $dateTime = new DateTime;
        $dateTime->setTimestamp($this->time + self::CACHE_TIME);
        return $dateTime;
    }

    /**
     * Valid until timestamp
     *
     * @return \DateTime|false
     */
    public function validUntil()
    {
        $dateTime = new DateTime;
        $dateTime->setTimestamp($this->time + max(self::CACHE_TIME, $this->maxAge));
        return $dateTime;
    }
}
