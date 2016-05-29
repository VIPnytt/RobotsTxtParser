<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Client\Directives\HostClient;
use vipnytt\RobotsTxtParser\Client\Directives\SitemapClient;
use vipnytt\RobotsTxtParser\Client\Directives\UserAgentClient;
use vipnytt\RobotsTxtParser\Client\Encoding\EncodingConverter;
use vipnytt\RobotsTxtParser\Parser\RobotsTxtParser;
use vipnytt\RobotsTxtParser\Parser\UrlParser;

/**
 * Class Input
 *
 * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt
 * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml
 * @link http://www.robotstxt.org/robotstxt.html
 * @link https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1
 * @link http://www.conman.org/people/spc/robots2.html
 *
 * @package vipnytt\RobotsTxtParser\Client
 */
class Input extends RobotsTxtParser
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
    private $userAgentClients = [];

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
    private function convertEncoding($encoding)
    {
        mb_internal_encoding(self::ENCODING);
        $convert = new EncodingConverter($this->content, $encoding);
        if (($result = $convert->auto()) !== false) {
            return $this->content = $result;
        }
        return $this->content;
    }

    /**
     * Byte limit
     *
     * @param int|null $bytes
     * @return string
     */
    private function limitBytes($bytes)
    {
        if ($bytes === null) {
            return $this->content;
        } elseif ($bytes < 5000) {
            trigger_error('Byte limit is set dangerously low!', E_USER_WARNING);
        }
        return $this->content = mb_strcut($this->content, 0, intval($bytes));
    }

    /**
     * Sitemaps
     *
     * @return SitemapClient
     */
    public function sitemap()
    {
        $export = $this->sitemap->export();
        return new SitemapClient(isset($export[self::DIRECTIVE_SITEMAP]) ? $export[self::DIRECTIVE_SITEMAP] : []);
    }

    /**
     * Host
     *
     * @return HostClient
     */
    public function host()
    {
        $export = $this->host->export();
        return new HostClient(isset($export[self::DIRECTIVE_HOST][0]) ? $export[self::DIRECTIVE_HOST][0] : null);
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
