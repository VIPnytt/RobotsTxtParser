<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Client\Directives\CleanParamClient;
use vipnytt\RobotsTxtParser\Client\Directives\HostClient;
use vipnytt\RobotsTxtParser\Client\Directives\SitemapClient;
use vipnytt\RobotsTxtParser\Client\Directives\UserAgentClient;
use vipnytt\RobotsTxtParser\Client\Encoding\EncodingConverter;
use vipnytt\RobotsTxtParser\Parser\RobotsTxtParser;
use vipnytt\RobotsTxtParser\Parser\UrlParser;

/**
 * Class Basic
 *
 * @package vipnytt\RobotsTxtParser
 */
class Basic extends RobotsTxtParser
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
     * Basic constructor.
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
        parent::__construct($this->base, $this->content);
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
     * Clean-param
     *
     * @return CleanParamClient
     */
    public function cleanParam()
    {
        return $this->handler->cleanParam()->client();
    }

    /**
     * Host
     *
     * @return HostClient
     */
    public function host()
    {
        return $this->handler->host()->client();
    }

    /**
     * Sitemaps
     *
     * @return SitemapClient
     */
    public function sitemap()
    {
        return $this->handler->sitemap()->client();
    }

    /**
     * Get User-agent list
     *
     * @return string[]
     */
    public function getUserAgents()
    {
        return $this->handler->userAgent()->getUserAgents();
    }

    /**
     * Client User-agent specific rules
     *
     * @param string $string
     * @return UserAgentClient
     */
    public function userAgent($string = self::USER_AGENT)
    {
        return $this->handler->userAgent()->client($string, $this->statusCode);
    }
}