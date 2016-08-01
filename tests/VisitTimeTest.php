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
 * Class VisitTimeTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class VisitTimeTest extends TestCase
{
    public function testVisitTimeIsClosed()
    {
        $robotsMorning = <<<ROBOTS
User-agent: *
Visit-time: 0800-1000
ROBOTS;
        $robotsEvening = <<<ROBOTS
User-agent: *
Visit-time: 2000-2200
ROBOTS;
        $robotsTxtContent = (int)gmdate('H') >= 12 ? $robotsMorning : $robotsEvening;
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertFalse($parser->userAgent()->visitTime()->isVisitTime());
        $this->assertTrue($parser->userAgent()->isDisallowed('/'));
    }

    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param array $result
     * @param string|false $rendered
     */
    public function testVisitTime($robotsTxtContent, $result, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent()->visitTime()->isVisitTime(gmmktime(9, 0, 0)));
        $this->assertTrue($parser->userAgent()->visitTime()->isVisitTime(gmmktime(14, 0, 0)));
        $this->assertTrue($parser->userAgent()->visitTime()->isVisitTime(gmmktime(19, 0, 0)));
        $this->assertFalse($parser->userAgent()->visitTime()->isVisitTime(gmmktime(11, 30, 0)));

        $this->assertEquals($result, $parser->userAgent('*')->visitTime()->export());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal("\n"));
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
User-agent: *
Visit-time: 0715-1100
Visit-time: 1200-1700
Visit-time: 1800-2045
RENDERED
            ]
        ];
    }
}
