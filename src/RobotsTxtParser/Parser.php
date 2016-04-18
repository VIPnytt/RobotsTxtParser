<?php
namespace vipnytt\RobotsTxtParser;

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
class Parser extends Core
{
    /**
     * HTTP status code parser
     * @var StatusCodeParser
     */
    protected $statusCodeParser;

    /**
     * Robots.txt origin
     * @var string
     */
    protected $origin;

    /**
     * Status code
     * @var int|null
     */
    protected $statusCode;

    /**
     * Parser constructor.
     *
     * @param string $robotsTxtURL
     * @param int|null $statusCode
     * @param string|null $content
     * @param string $encoding
     * @param int $byteLimit
     */
    public function __construct($robotsTxtURL, $statusCode, $content, $encoding = self::ENCODING, $byteLimit = self::BYTE_LIMIT)
    {
        parent::__construct($content, $encoding, $byteLimit);
        $this->origin = $robotsTxtURL;
        $this->statusCode = $statusCode;
    }

    /**
     * Get sitemaps
     *
     * @return array
     */
    public function getSitemaps()
    {
        return $this->sitemap->export();
    }

    /**
     * Get host
     *
     * @return string|null
     */
    public function getHost()
    {
        return $this->host->export();
    }

    /**
     * Get Clean-param
     *
     * @return array
     */
    public function getCleanParam()
    {
        return $this->cleanParam->export();
    }

    /**
     * Return an User-agent instance, for future usage
     *
     * @param string $string
     * @return UserAgentClient
     */
    public function userAgent($string = self::USER_AGENT)
    {
        $userAgentParser = new UserAgentParser($string);
        if (($userAgent = $userAgentParser->match($this->userAgent->userAgents)) === false) {
            $userAgent = self::USER_AGENT;
        }
        return new UserAgentClient($this->userAgent->{self::DIRECTIVE_ALLOW}[$userAgent], $this->userAgent->{self::DIRECTIVE_DISALLOW}[$userAgent], $userAgent, $this->origin, $this->statusCode);
    }
}
