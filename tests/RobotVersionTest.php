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
 * Class RobotVersionTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class RobotVersionTest extends TestCase
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
            $this->assertEquals($rendered, $parser->render()->normal("\n"));
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
                                    'noindex' => [],
                                    'disallow' => [],
                                    'allow' => [],
                                    'crawl-delay' => null,
                                    'cache-delay' => null,
                                    'request-rate' => [],
                                    'comment' => [],
                                ],
                            'googlebot' =>
                                [
                                    'robot-version' => '1.0',
                                    'visit-time' => [],
                                    'noindex' => [],
                                    'disallow' => [],
                                    'allow' => [],
                                    'crawl-delay' => null,
                                    'cache-delay' => null,
                                    'request-rate' => [],
                                    'comment' => [],
                                ],
                        ],
                ],
                <<<RENDERED
User-agent: *
Robot-version: 2.0

User-agent: googlebot
Robot-version: 1.0
RENDERED
            ]
        ];
    }
}
