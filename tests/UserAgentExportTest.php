<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class UserAgentExportTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class UserAgentExportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param array $result
     */
    public function testUserAgentExport($robotsTxtContent, $result)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertEquals($result, $parser->userAgent('googlebot')->export());
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
Disallow /private/

User-agent: Googlebot
Disallow: /admin/
Allow: /public/
Crawl-delay: 5

User-agent: BingBot
Disallow: /
ROBOTS
                ,
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
                ]
            ],
        ];
    }
}
