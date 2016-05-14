<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class VisitTimeTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class VisitTimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param array $result
     */
    public function testVisitTime($robotsTxtContent, $result)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertEquals($result, $parser->userAgent('*')->getVisitTime());
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
Visit-time: 0715-1100
Visit-time: 12.00-17.00
Visit-time: 18:00-20:45
Visit-time: 11-12 # invalid
ROBOTS
                ,
                [
                    [
                        'from' => '0715',
                        'to' => '1100',
                    ],
                    [
                        'from' => '1200',
                        'to' => '1700',
                    ],
                    [
                        'from' => '1800',
                        'to' => '2045',
                    ],
                ]
            ]
        ];
    }
}
