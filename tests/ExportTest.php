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
     */
    public function testExport($robotsTxtContent, $result)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertEquals($result, $parser->export());
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
Robot-version: 2.0
Disallow: /admin/
Allow: /public/
Crawl-delay: 5

User-agent: Googlebot
Robot-version: 1.0
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
                                    'robot-version' => '2.0',
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
                                    'robot-version' => '1.0',
                                ],
                        ],
                ]
            ]
        ];
    }
}
