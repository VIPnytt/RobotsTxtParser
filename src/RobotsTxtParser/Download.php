<?php
namespace vipnytt\RobotsTxtParser;

use GuzzleHttp;
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
     * GuzzleHttp response
     * @var \Psr\Http\Message\ResponseInterface
     */
    private $response;

    /**
     * Download constructor.
     *
     * @param string $baseUri
     * @param array $guzzleConfig
     */
    public function __construct($baseUri, $guzzleConfig = [])
    {
        $this->baseUri = $baseUri;
        $client = new GuzzleHttp\Client(
            array_merge_recursive(
                [
                    'allow_redirects' => [
                        'max' => self::MAX_REDIRECTS,
                    ],
                    'base_uri' => $baseUri,
                    'headers' => [
                        'Accept' => 'text/plain;q=1.0, text/*;q=0.8, */*;q=0.1',
                        'Accept-Charset' => 'utf-8;q=1.0, *;q=0.1',
                        'Accept-Encoding' => 'identity;q=1.0, *;q=0.1',
                        'User-Agent' => 'RobotsTxtParser-VIPnytt/1.0 (+https://github.com/VIPnytt/RobotsTxtParser/blob/master/README.md)',
                    ],
                    'http_errors' => false,
                    'timeout' => 60,
                ],
                $guzzleConfig
            )
        );
        $this->response = $client->request('GET', '/robots.txt');
    }

    /**
     * Parser client
     *
     * @param int|null $byteLimit
     * @return Client
     */
    public function parserClient($byteLimit = self::BYTE_LIMIT)
    {
        return new Client($this->baseUri, $this->getStatusCode(), $this->getContents(), $this->getEncoding(), $byteLimit);
    }

    /**
     * Status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * URL content
     *
     * @return string
     */
    public function getContents()
    {
        return $this->response->getBody()->getContents();
    }

    /**
     * Encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        $header = $this->response->getHeader('content-type')[0];
        $split = array_map('trim', mb_split(';', $header));
        foreach ($split as $string) {
            if (mb_stripos($string, 'charset=') === 0) {
                return mb_split('=', $string, 2)[1];
            }
        }
        return $this->detectEncoding();
    }

    /**
     * Manually detect encoding
     *
     * @return string
     */
    protected function detectEncoding()
    {
        if (($encoding = mb_detect_encoding($this->getContents())) !== false) {
            return $encoding;
        }
        return self::ENCODING;
    }
}
