<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Tests;

use PHPUnit\Framework\TestCase;
use vipnytt\RobotsTxtParser;

/**
 * Class SitemapTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class SitemapTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param array $result
     * @param string|false $rendered
     */
    public function testSitemap($robotsTxtContent, $result, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertEquals($result, $parser->sitemap()->export());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal("\n"));
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
Sitemap: http://example.com/sitemap.xml.gz
Sitemap: http://example.com/sitemap.xml?year=2014
Sitemap: http://example.com/sitemap.xml?year=2015
Sitemap: http://internet.com/sitemap.xml
Sitemap: http://somesite.com/sitemap.xml
Sitemap: http://worldwideweb.com/sitemap.xml

User-agent: *
Disallow: /admin/
RENDERED
            ]
        ];
    }
}
