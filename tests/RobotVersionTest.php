<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

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
     */
    public function testRobotVersion($robotsTxtContent, $result)
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
Robot-version: 2.0.0
Robot-version: 2

User-agent: Googlebot
Robot-version: 1.0
ROBOTS
                ,
                [
                    'user-agent' =>
                        [
                            '*' =>
                                [
                                    'robot-version' => '2.0',
                                ],
                            'googlebot' =>
                                [
                                    'robot-version' => '1.0',
                                ],
                        ],
                ]
            ]
        ];
    }
}
