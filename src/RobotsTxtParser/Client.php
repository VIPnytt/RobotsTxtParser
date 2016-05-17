<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Client\UserAgentClient;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\UserAgentParser;

/**
 * Class Parser
 *
 * @link https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt
 * @link https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml
 * @link http://www.robotstxt.org/robotstxt.html
 * @link https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1
 * @link http://www.conman.org/people/spc/robots2.html
 *
 * @package vipnytt\RobotsTxtParser
 */
class Client extends Parser
{
    use UrlParser;

    /**
     * Robots.txt base uri
     * @var string
     */
    protected $baseUri;

    /**
     * Status code
     * @var int|null
     */
    protected $statusCode;

    /**
     * UserAgentClient class cache
     * @var UserAgentClient[]
     */
    protected $userAgentClients = [];

    /**
     * Parser constructor.
     *
     * @param string $baseUri
     * @param int|null $statusCode
     * @param string|null $content
     * @param string $encoding
     * @param int|null $byteLimit
     */
    public function __construct($baseUri, $statusCode = null, $content = null, $encoding = self::ENCODING, $byteLimit = self::BYTE_LIMIT)
    {
        $this->baseUri = $this->urlBase($this->urlEncode($baseUri));
        $this->statusCode = $statusCode;
        if ($content === null) {
            $client = new Download($this->baseUri);
            $this->statusCode = $client->getStatusCode();
            $content = $client->getContents();
            $encoding = $client->getEncoding();
        }
        parent::__construct($content, $encoding, $byteLimit);
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
        if (isset($this->userAgentClients[$string])) {
            return $this->userAgentClients[$string];
        }
        $userAgentParser = new UserAgentParser(mb_strtolower($string));
        if (($userAgent = $userAgentParser->match($this->userAgent->userAgents)) === false) {
            $userAgent = self::USER_AGENT;
        }
        $rules = [
            self::DIRECTIVE_ALLOW => $this->userAgent->allow[$userAgent],
            self::DIRECTIVE_CACHE_DELAY => $this->userAgent->cacheDelay[$userAgent],
            self::DIRECTIVE_COMMENT => $this->userAgent->comment[$userAgent],
            self::DIRECTIVE_CRAWL_DELAY => $this->userAgent->crawlDelay[$userAgent],
            self::DIRECTIVE_DISALLOW => $this->userAgent->disallow[$userAgent],
            self::DIRECTIVE_REQUEST_RATE => $this->userAgent->requestRate[$userAgent],
            self::DIRECTIVE_ROBOT_VERSION => $this->userAgent->robotVersion[$userAgent],
            self::DIRECTIVE_VISIT_TIME => $this->userAgent->visitTime[$userAgent],
        ];
        $this->userAgentClients[$string] = new UserAgentClient($rules, $userAgent, $this->baseUri, $this->statusCode);
        return $this->userAgentClients[$string];
    }
}
