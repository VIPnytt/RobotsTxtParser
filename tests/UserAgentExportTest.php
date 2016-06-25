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
     * @param array $rules
     * @param array $userAgentList
     * @param string|false $rendered
     */
    public function testUserAgentExport($robotsTxtContent, $rules, $userAgentList, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertEquals($rules, $parser->export());
        $this->assertEquals($userAgentList, $parser->getUserAgents());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
            $this->testUserAgentExport($rendered, $rules, $userAgentList, false);
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

UserAgent: BingBot
Disallow: /
ROBOTS
                ,
                [
                    'host' => null,
                    'clean-param' => [],
                    'sitemap' => [],
                    'user-agent' => [
                        'bingbot' => [
                            'robot-version' => null,
                            'visit-time' => [],
                            'disallow' =>
                                [
                                    'host' => [],
                                    'path' =>
                                        [
                                            '/',
                                        ],
                                    'clean-param' => [],
                                ],
                            'allow' =>
                                [
                                    'host' => [],
                                    'path' => [],
                                    'clean-param' => [],
                                ],
                            'crawl-delay' => null,
                            'cache-delay' => null,
                            'request-rate' => [],
                            'comment' => [],
                        ],
                        'googlebot' =>
                            [
                                'robot-version' => null,
                                'visit-time' => [],
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
                            ]
                    ]
                ],
                [
                    'bingbot',
                    'googlebot',
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
