<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

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
        $parser = new RobotsTxtParser\Core('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Core', $parser);

        $this->assertEquals($result, $parser->userAgent('googlebot')->getRules());

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
                    'disallow' =>
                        [
                            'path' =>
                                [
                                    '/admin/',
                                ],
                        ],
                    'allow' =>
                        [
                            'path' =>
                                [
                                    '/public/',
                                ],
                        ],
                    'crawl-delay' => 5,
                ],
                <<<RENDERED
user-agent:bingbot
disallow:/
user-agent:googlebot
disallow:/admin/
allow:/public/
crawl-delay:5
RENDERED
            ],
        ];
    }
}
