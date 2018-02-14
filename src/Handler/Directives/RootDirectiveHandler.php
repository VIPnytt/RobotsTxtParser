<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

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
    public $cleanParam;

    /**
     * Host
     * @var HostParser
     */
    public $host;

    /**
     * Sitemap
     * @var SitemapParser
     */
    public $sitemap;

    /**
     * User-agent
     * @var UserAgentParser
     */
    public $userAgent;

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
        $this->userAgent = new UserAgentParser($base);
    }
}
