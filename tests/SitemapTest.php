<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

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
     * @param array $result
     * @param string|false $rendered
     */
    public function testSitemap($robotsTxtContent, $result, $rendered)
    {
        $parser = new RobotsTxtParser\Basic('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Basic', $parser);

        $this->assertEquals($result, $parser->sitemap()->export());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
            sort($result);
            $this->testSitemap($rendered, $result, false);
        }
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
                ],
                <<<RENDERED
sitemap:http://example.com/sitemap.xml.gz
sitemap:http://example.com/sitemap.xml?year=2014
sitemap:http://example.com/sitemap.xml?year=2015
sitemap:http://internet.com/sitemap.xml
sitemap:http://somesite.com/sitemap.xml
sitemap:http://worldwideweb.com/sitemap.xml
user-agent:*
disallow:/admin/
RENDERED
            ]
        ];
    }
}
