<?php
namespace vipnytt\RobotsTxtParser\Client;

use GuzzleHttp;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class Download
 *
 * @package vipnytt\RobotsTxtParser\Client
 */
class Download implements RobotsTxtInterface
{
    use UrlParser;

    private $response;

    /**
     * Download constructor.
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $client = new GuzzleHttp\Client(
            [
                'base_uri' => $this->urlBase($url),
                'max' => self::MAX_REDIRECTS,
                'http_errors' => false,
            ]
        );
        $this->response = $client->request('GET', '/robots.txt');
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
        if (($encoding = mb_detect_encoding($this->getBody())) !== false) {
            return $encoding;
        }
        return self::ENCODING;
    }

    /**
     * URL content
     *
     * @return string
     */
    public function getBody()
    {
        return $this->response->getBody()->getContents();
    }
}
