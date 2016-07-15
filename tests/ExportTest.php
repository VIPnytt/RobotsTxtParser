<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

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
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertEquals($result, $parser->export());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
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
Sitemap: HTTP://EXAMPLE.COM/sitemap.xml.gz
ROBOTS
                ,
                [
                    'host' => 'example.com',
                    'clean-param' => [],
                    'sitemap' =>
                        [
                            'http://example.com/sitemap.xml',
                            'http://example.com/sitemap.xml.gz',
                        ],
                    'user-agent' =>
                        [
                            '*' =>
                                [
                                    'robot-version' => null,
                                    'visit-time' => [],
                                    'noindex' => [
                                        'host' => [],
                                        'path' => [],
                                        'clean-param' => [],
                                    ],
                                    'disallow' =>
                                        [
                                            'host' => [],
                                            'path' =>
                                                [
                                                    '/admin/',
                                                ],
                                            'clean-param' => [],
                                        ],
                                    'allow' =>
                                        [
                                            'host' => [],
                                            'path' =>
                                                [
                                                    '/public/',
                                                ],
                                            'clean-param' => [],
                                        ],
                                    'crawl-delay' => 5,
                                    'cache-delay' => null,
                                    'request-rate' => [],
                                    'comment' => [],
                                ],
                            'googlebot' =>
                                [
                                    'robot-version' => null,
                                    'visit-time' => [],
                                    'noindex' => [
                                        'host' => [],
                                        'path' => [],
                                        'clean-param' => [],
                                    ],
                                    'disallow' =>
                                        [
                                            'host' => [],
                                            'path' =>
                                                [
                                                    '/',
                                                ],
                                            'clean-param' => [],
                                        ],
                                    'allow' => [
                                        'host' => [],
                                        'path' => [],
                                        'clean-param' => [],
                                    ],
                                    'crawl-delay' => null,
                                    'cache-delay' => null,
                                    'request-rate' => [],
                                    'comment' => [],
                                ],
                        ],
                ],
                <<<RENDERED
host:example.com
sitemap:http://example.com/sitemap.xml
sitemap:http://example.com/sitemap.xml.gz
user-agent:*
disallow:/admin/
allow:/public/
crawl-delay:5
user-agent:googlebot
disallow:/
RENDERED
            ]
        ];
    }
}
