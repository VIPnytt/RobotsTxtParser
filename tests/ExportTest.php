<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class ExportTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class ExportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param array $result
     * @param string|false $rendered
     */
    public function testExport($robotsTxtContent, $result, $rendered)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertEquals($result, $parser->export());

        if ($rendered !== false) {
            $this->assertSame($rendered, $parser->render());
            $this->testExport($rendered, $result, false);
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
User-agent: *
Disallow: /admin/
Allow: /public/
Crawl-delay: 5

User-agent: Googlebot
Disallow: /

Host: example.com

Sitemap: http://example.com/sitemap.xml
Sitemap: http://example.com/sitemap.xml.gz
ROBOTS
                ,
                [
                    'host' =>
                        [
                            'example.com',
                        ],
                    'sitemap' =>
                        [
                            'http://example.com/sitemap.xml',
                            'http://example.com/sitemap.xml.gz',
                        ],
                    'user-agent' =>
                        [
                            '*' =>
                                [
                                    'allow' =>
                                        [
                                            'path' =>
                                                [
                                                    '/public/',
                                                ],
                                        ],
                                    'crawl-delay' => 5,
                                    'disallow' =>
                                        [
                                            'path' =>
                                                [
                                                    '/admin/',
                                                ],
                                        ],
                                ],
                            'googlebot' =>
                                [
                                    'disallow' =>
                                        [
                                            'path' =>
                                                [
                                                    '/',
                                                ],
                                        ],
                                ],
                        ],
                ],
                <<<RENDERED
host:example.com
sitemap:http://example.com/sitemap.xml
sitemap:http://example.com/sitemap.xml.gz
user-agent:*
allow:/public/
crawl-delay:5
disallow:/admin/
user-agent:googlebot
disallow:/
RENDERED
            ]
        ];
    }
}
