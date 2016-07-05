<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Client\Directives\CleanParamClient;
use vipnytt\RobotsTxtParser\Client\Directives\HostClient;
use vipnytt\RobotsTxtParser\Client\Directives\SitemapClient;
use vipnytt\RobotsTxtParser\Client\Directives\UserAgentClient;
use vipnytt\RobotsTxtParser\Client\Encoding\EncodingConverter;
use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\RobotsTxtParser\Parser\RobotsTxtParser;
use vipnytt\RobotsTxtParser\Parser\UriParser;

/**
 * Class TxtClient
 *
 * @package vipnytt\RobotsTxtParser
 */
class TxtClient extends RobotsTxtParser
{
    use UriParser;

    /**
     * Status code
     * @var int|null
     */
    private $statusCode;

    /**
     * Robots.txt content
     * @var string
     */
    private $content;

    /**
     * Encoding
     * @var string
     */
    private $encoding;

    /**
     * TxtClient constructor.
     *
     * @param string $baseUri
     * @param int $statusCode
     * @param string $content
     * @param string $encoding
     * @param string|null $effectiveUri
     * @param int|null $byteLimit
     */
    public function __construct($baseUri, $statusCode, $content, $encoding = self::ENCODING, $effectiveUri = null, $byteLimit = self::BYTE_LIMIT)
    {
        $this->statusCode = $statusCode;
        $this->content = $content;
        $this->encoding = $encoding;
        $this->convertEncoding();
        $this->limitBytes($byteLimit);
        parent::__construct($baseUri, $this->content, $effectiveUri);
    }

    /**
     * Convert character encoding
     *
     * @return string
     */
    private function convertEncoding()
    {
        mb_internal_encoding(self::ENCODING);
        $convert = new EncodingConverter($this->content, $this->encoding);
        if (($result = $convert->auto()) !== false) {
            $this->encoding = self::ENCODING;
            return $this->content = $result;
        }
        return $this->content;
    }

    /**
     * Byte limit
     *
     * @param int|null $bytes
     * @return string
     * @throws ClientException
     */
    private function limitBytes($bytes)
    {
        if ($bytes === null) {
            return $this->content;
        } elseif ($bytes < (self::BYTE_LIMIT * 0.25)) {
            throw new ClientException('Byte limit is set dangerously low! Default value=' . self::BYTE_LIMIT);
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
