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
