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
     * @param string|false $rendered
     */
    public function testUserAgentExport($robotsTxtContent, $result, $rendered)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Client', $parser);

        $this->assertEquals($result, $parser->userAgent('googlebot')->export());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
            $this->testUserAgentExport($rendered, $result, false);
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
                ],
                <<<RENDERED
user-agent:bingbot
disallow:/
user-agent:googlebot
allow:/public/
crawl-delay:5
disallow:/admin/
RENDERED
            ],
        ];
    }
}
