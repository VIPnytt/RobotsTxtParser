<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class SitemapClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class SitemapClient
{
    /**
     * Sitemaps
     * @var string[]
     */
    private $sitemaps = [];

    /**
     * SitemapClient constructor.
     *
     * @param string[] $sitemaps
     */
    public function __construct(array $sitemaps)
    {
        $this->sitemaps = $sitemaps;
    }

    /**
     * Export
     *
     * @return string[]
     */
    public function export()
    {
        return $this->sitemaps;
    }
}
