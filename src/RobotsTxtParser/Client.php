<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Client\Download;
use vipnytt\RobotsTxtParser\Client\UserAgentClient;
use vipnytt\RobotsTxtParser\Parser\StatusCodeParser;
use vipnytt\UserAgentParser;

/**
 * Class Parser
 *
 * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt
 * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml
 * @link http://www.robotstxt.org/robotstxt.html
 * @link https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1
 *
 * @package vipnytt\RobotsTxtParser
 */
class Client extends Parser
{
    /**
     * Status code
     * @var int|null
     */
    public $statusCode;
    /**
     * Robots.txt content
     * @var string
     */
    public $content;
    /**
     * Content encoding
     * @var string
     */
    public $encoding;
    /**
     * HTTP status code parser
     * @var StatusCodeParser
     */
    protected $statusCodeParser;
    /**
     * Robots.txt base
     * @var string
     */
    protected $baseUrl;

    /**
     * Parser constructor.
     *
     * @param string $baseUrl
     * @param int|null $statusCode
     * @param string|null $content
     * @param string $encoding
     * @param int $byteLimit
     */
    public function __construct($baseUrl, $statusCode = null, $content = null, $encoding = self::ENCODING, $byteLimit = self::BYTE_LIMIT)
    {
        $this->baseUrl = $baseUrl;
        $this->statusCode = $statusCode;
        $this->content = $content;
        $this->encoding = $encoding;
        if ($content === null) {
            $client = new Download($this->baseUrl);
            $this->statusCode = $client->getStatusCode();
            $this->content = $client->getBody();
            $this->encoding = $client->getEncoding();
        }
        parent::__construct($this->content, $this->encoding, $byteLimit);
    }

    /**
     * Get sitemaps
     *
     * @return array
     */
    public function getSitemaps()
    {
        if (isset($this->sitemap->export()[self::DIRECTIVE_SITEMAP])) {
            return $this->sitemap->export()[self::DIRECTIVE_SITEMAP];
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
        if (isset($this->host->export()[self::DIRECTIVE_HOST])) {
            return $this->host->export()[self::DIRECTIVE_HOST][0];
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
        if (isset($this->cleanParam->export()[self::DIRECTIVE_CLEAN_PARAM])) {
            return $this->cleanParam->export()[self::DIRECTIVE_CLEAN_PARAM];
        }
        return null;
    }

    /**
     * Return an User-agent instance, for future usage
     *
     * @param string $string
     * @return UserAgentClient
     */
    public function userAgent($string = self::USER_AGENT)
    {
        $userAgentParser = new UserAgentParser(mb_strtolower($string));
        if (($userAgent = $userAgentParser->match($this->userAgent->userAgents)) === false) {
            $userAgent = self::USER_AGENT;
        }
        $rules = [
            self::DIRECTIVE_ALLOW => $this->userAgent->allow[$userAgent],
            self::DIRECTIVE_DISALLOW => $this->userAgent->disallow[$userAgent],
            self::DIRECTIVE_CRAWL_DELAY => $this->userAgent->crawlDelay[$userAgent],
            self::DIRECTIVE_CACHE_DELAY => $this->userAgent->cacheDelay[$userAgent],
        ];
        return new UserAgentClient($rules, $userAgent, $this->baseUrl, $this->statusCode);
    }
}
