<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\Client\Directives\SitemapClient;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class SitemapParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class SitemapParser implements ParserInterface, RobotsTxtInterface
{
    use UrlParser;

    /**
     * Sitemap array
     * @var string[]
     */
    private $array = [];

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
            !$this->urlValidate(($url = $this->urlEncode($line))) ||
            in_array($url, $this->array)
        ) {
            return false;
        }
        $this->array[] = $url;
        return true;
    }

    /**
     * Client
     *
     * @return SitemapClient
     */
    public function client()
    {
        return new SitemapClient($this->array);
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        $result = [];
        foreach ($this->array as $value) {
            $result[] = self::DIRECTIVE_SITEMAP . ':' . $value;
        }
        sort($result);
        return $result;
    }
}
