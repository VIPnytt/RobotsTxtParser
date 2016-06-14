<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

/**
 * Class RobotVersionTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class RobotVersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param array $result
     * @param string|false $rendered
     */
    public function testRobotVersion($robotsTxtContent, $result, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertEquals($result, $parser->export());
        $this->assertEquals($result['user-agent']['*']['robot-version'], $parser->userAgent()->robotVersion()->export());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
            $this->testRobotVersion($rendered, $result, false);
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
Robot-version: 2.0
Robot-version: 2.0.0
Robot-version: 2

User-agent: Googlebot
Robot-version: 1.0
ROBOTS
                ,
                [
                    'host' => null,
                    'clean-param' => [],
                    'sitemap' => [],
                    'user-agent' =>
                        [
                            '*' =>
                                [
                                    'robot-version' => '2.0',
                                    'visit-time' => [],
                                    'disallow' => [
                                        'host' => [],
                                        'path' => [],
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
                            'googlebot' =>
                                [
                                    'robot-version' => '1.0',
                                    'visit-time' => [],
                                    'disallow' => [
                                        'host' => [],
                                        'path' => [],
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
user-agent:*
robot-version:2.0
user-agent:googlebot
robot-version:1.0
RENDERED
            ]
        ];
    }
}
