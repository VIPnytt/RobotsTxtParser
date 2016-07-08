<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\SitemapClient;
use vipnytt\RobotsTxtParser\Parser\UriParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class SitemapParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class SitemapParser implements ParserInterface, RobotsTxtInterface
{
    use UriParser;

    /**
     * Sitemap array
     * @var string[]
     */
    private $sitemaps = [];

    /**
     * Sitemap constructor.
     */
    public function __construct()
    {
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        if (
            !$this->uriValidate(($uri = $this->uriEncode($line))) ||
            in_array($uri, $this->sitemaps)
        ) {
            return false;
        }
        $this->sitemaps[] = $uri;
        return true;
    }

    /**
     * Client
     *
     * @return SitemapClient
     */
    public function client()
    {
        return new SitemapClient($this->sitemaps);
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        $result = [];
        foreach ($this->sitemaps as $uri) {
            $result[] = self::DIRECTIVE_SITEMAP . ':' . $uri;
        }
        sort($result);
        return $result;
    }
}
