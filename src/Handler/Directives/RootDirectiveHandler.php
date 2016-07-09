<?php
namespace vipnytt\RobotsTxtParser\Handler\Directives;

use vipnytt\RobotsTxtParser\Parser\Directives\CleanParamParser;
use vipnytt\RobotsTxtParser\Parser\Directives\HostParser;
use vipnytt\RobotsTxtParser\Parser\Directives\SitemapParser;
use vipnytt\RobotsTxtParser\Parser\Directives\UserAgentParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class RootDirectiveHandler
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class RootDirectiveHandler implements RobotsTxtInterface
{
    /**
     * Clean-param
     * @var CleanParamParser
     */
    private $cleanParam;

    /**
     * Host
     * @var HostParser
     */
    private $host;

    /**
     * Sitemap
     * @var SitemapParser
     */
    private $sitemap;

    /**
     * User-agent
     * @var UserAgentParser
     */
    private $userAgent;

    /**
     * RootDirectiveHandler constructor.
     *
     * @param string $base
     * @param string $effective
     */
    public function __construct($base, $effective)
    {
        $this->cleanParam = new CleanParamParser();
        $this->host = new HostParser($base, $effective);
        $this->sitemap = new SitemapParser();
        $this->userAgent = new UserAgentParser($base, $effective);
    }

    /**
     * Clean-param
     *
     * @return CleanParamParser
     */
    public function cleanParam()
    {
        return $this->cleanParam;
    }

    /**
     * Host
     *
     * @return HostParser
     */
    public function host()
    {
        return $this->host;
    }

    /**
     * Sitemap
     *
     * @return SitemapParser
     */
    public function sitemap()
    {
        return $this->sitemap;
    }

    /**
     * User-agent
     *
     * @return UserAgentParser
     */
    public function userAgent()
    {
        return $this->userAgent;
    }
}
