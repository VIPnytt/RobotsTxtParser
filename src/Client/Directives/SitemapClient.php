<?php
namespace vipnytt\RobotsTxtParser\Client\Directives;

/**
 * Class SitemapClient
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/SitemapClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Directives
 */
class SitemapClient implements ClientInterface
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
