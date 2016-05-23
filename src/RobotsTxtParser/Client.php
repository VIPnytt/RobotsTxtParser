<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Client\UserAgentClient;
use vipnytt\RobotsTxtParser\Core\CharacterEncodingConvert;
use vipnytt\RobotsTxtParser\Core\UrlParser;
use vipnytt\RobotsTxtParser\Exceptions\ClientException;

/**
 * Class Core
 *
 * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt
 * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml
 * @link http://www.robotstxt.org/robotstxt.html
 * @link https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1
 * @link http://www.conman.org/people/spc/robots2.html
 *
 * @package vipnytt\RobotsTxtParser
 */
class Client extends Core
{
    use UrlParser;

    /**
     * Base uri
     * @var string
     */
    protected $base;

    /**
     * Status code
     * @var int|null
     */
    protected $statusCode;

    /**
     * Robots.txt content
     * @var string
     */
    protected $content;

    /**
     * UserAgentClient class cache
     * @var UserAgentClient[]
     */
    protected $userAgentClients = [];

    /**
     * Core constructor.
     *
     * @param string $baseUri
     * @param int $statusCode
     * @param string|null $content
     * @param string $encoding
     * @param int|null $byteLimit
     */
    public function __construct($baseUri, $statusCode, $content, $encoding = self::ENCODING, $byteLimit = self::BYTE_LIMIT)
    {
        $this->base = $this->urlBase($this->urlEncode($baseUri));
        $this->statusCode = $statusCode;
        $this->content = $content;
        $this->convertEncoding($encoding);
        $this->limitBytes($byteLimit);
        parent::__construct($this->content);
    }

    /**
     * Convert character encoding
     *
     * @param string $encoding
     * @return string
     */
    protected function convertEncoding($encoding)
    {
        mb_internal_encoding(self::ENCODING);
        $convert = new CharacterEncodingConvert($this->content, $encoding);
        if (($result = $convert->auto()) !== false) {
            return $this->content = $result;
        }
        return $this->content;
    }

    /**
     * Byte limit
     *
     * @param $bytes
     * @return string
     * @throws ClientException
     */
    protected function limitBytes($bytes)
    {
        if (is_numeric($bytes) && $bytes < 5000) {
            throw new ClientException('Byte limit is set far too low');
        }
        return $this->content = mb_strcut($this->content, 0, $bytes);
    }

    /**
     * Get sitemaps
     *
     * @return array
     */
    public function getSitemaps()
    {
        $export = $this->sitemap->export();
        if (isset($export[self::DIRECTIVE_SITEMAP])) {
            return $export[self::DIRECTIVE_SITEMAP];
        }
        return [];
    }

    /**
     * Get host
     *
     * @return string|null
     */
    public function getHost()
    {
        $export = $this->host->export();
        if (isset($export[self::DIRECTIVE_HOST][0])) {
            return $export[self::DIRECTIVE_HOST][0];
        }
        return null;
    }

    /**
     * Get Clean-param
     *
     * @return array
     */
    public function getCleanParam()
    {
        $export = $this->cleanParam->export();
        if (isset($export[self::DIRECTIVE_CLEAN_PARAM])) {
            return $export[self::DIRECTIVE_CLEAN_PARAM];
        }
        return [];
    }

    /**
     * Get User-agent list
     *
     * @return array
     */
    public function getUserAgents()
    {
        return $this->userAgent->userAgents;
    }

    /**
     * Client User-agent specific rules
     *
     * @param string $string
     * @return UserAgentClient
     */
    public function userAgent($string = self::USER_AGENT)
    {
        if (isset($this->userAgentClients[$string])) {
            return $this->userAgentClients[$string];
        }
        $this->userAgentClients[$string] = new UserAgentClient($string, $this->userAgent, $this->base, $this->statusCode);
        return $this->userAgentClients[$string];
    }
}
