<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

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
     * @param string|false $rendered
     */
    public function testVisitTime($robotsTxtContent, $result, $rendered)
    {
        $parser = new RobotsTxtParser\Input('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Input', $parser);

        $this->assertEquals($result, $parser->userAgent('*')->visitTime()->export());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
            $this->testVisitTime($rendered, $result, false);
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
                ],
                <<<RENDERED
user-agent:*
visit-time:0715-1100
visit-time:1200-1700
visit-time:1800-2045
RENDERED
            ]
        ];
    }
}
