<?php
namespace vipnytt\RobotsTxtParser;

use vipnytt\UserAgentParser;

class Parser extends Core
{
    /**
     * HTTP status code parser
     * @var StatusCodeParser
     */
    protected $statusCodeParser;

    protected $origin;
    protected $statusCode;

    public function __construct($RobotsTxtURL, $statusCode, $content, $encoding = self::ENCODING, $byteLimit = self::BYTE_LIMIT)
    {
        parent::__construct($content, $encoding = self::ENCODING, $byteLimit = self::BYTE_LIMIT);
        $this->origin = $RobotsTxtURL;
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

    public function optimizeURL($url)
    {
        return $this->host->optimize($url);
    }

    public function userAgent($string = self::USER_AGENT)
    {
        $uaParser = new UserAgentParser($string);
        $userAgent = $uaParser->match($this->userAgent->userAgents, self::USER_AGENT);
        return new UserAgentClient([
            self::DIRECTIVE_DISALLOW => $this->userAgent->{self::DIRECTIVE_DISALLOW}[$userAgent],
            self::DIRECTIVE_ALLOW => $this->userAgent->{self::DIRECTIVE_ALLOW}[$userAgent],
        ], $userAgent, $this->origin, $this->statusCode);
    }
}
