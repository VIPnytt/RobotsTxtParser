<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class SitemapTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class SitemapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param array $sitemapArray
     */
    public function testSitemap($robotsTxtContent, $sitemapArray)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertEquals($sitemapArray, $parser->getSitemaps());
    }

    /**
     * Generate test data
     *
     * @return array
     */
    public function generateDataForTest()
    {
        return [
            [
                <<<ROBOTS
Sitemap: http://example.com/sitemap.xml?year=2015

User-agent: *
Disallow: /admin/
Sitemap: http://somesite.com/sitemap.xml

User-agent: Googlebot
Sitemap: http://internet.com/sitemap.xml
Sitemap: http://example.com/sitemap.xml.gz

User-agent: Yahoo
Sitemap: http://worldwideweb.com/sitemap.xml
Sitemap: http://example.com/sitemap.xml?year=2014
Sitemap: http://example.com/sitemap.xml.gz
ROBOTS
                ,
                [
                    'http://example.com/sitemap.xml?year=2015',
                    'http://somesite.com/sitemap.xml',
                    'http://internet.com/sitemap.xml',
                    'http://example.com/sitemap.xml.gz',
                    'http://worldwideweb.com/sitemap.xml',
                    'http://example.com/sitemap.xml?year=2014',
                ]
            ]
        ];
    }
}
