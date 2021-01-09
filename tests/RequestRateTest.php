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
 * Class RequestRateTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class RequestRateTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param array $result
     * @param string|false $rendered
     */
    public function testRequestRate($robotsTxtContent, $result, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $validRates = [];
        foreach ($result as $value) {
            $validRates[] = $value['rate'];
        }
        $this->assertContains($parser->userAgent('Legacy')->requestRate()->getValue(), $validRates);

        if ($rendered !== false) {
            $this->assertEquals($result, $parser->userAgent('*')->requestRate()->export());
            $this->assertEquals($rendered, $parser->render()->normal("\n"));
            $this->testRequestRate($rendered, $result, false);
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
Request-rate: 1/1s 2200-0600
Request-rate: 1/1h 0230-0330
Request-rate: 8/2m 07:00-21:00
Request-rate: 1200/3h 09.00-15.00
Request-rate: 9216/4d 07-21 # invalid time
Request-rate: 5 # invalid rate
ROBOTS
                ,
                [
                    [
                        'rate' => 3600,
                        'ratio' => '1/1h',
                        'from' => '0230',
                        'to' => '0330',
                    ],
                    [
                        'rate' => 37.5,
                        'ratio' => '2/75s',
                        'from' => null,
                        'to' => null,
                    ],
                    [
                        'rate' => 15,
                        'ratio' => '1/15s',
                        'from' => '0700',
                        'to' => '2100',
                    ],
                    [
                        'rate' => 9,
                        'ratio' => '1/9s',
                        'from' => '0900',
                        'to' => '1500',
                    ],
                    [
                        'rate' => 1,
                        'ratio' => '1/1s',
                        'from' => '2200',
                        'to' => '0600',
                    ],
                ],
                <<<RENDERED
User-agent: *
Request-rate: 1/1h 0230-0330
Request-rate: 2/75s
Request-rate: 1/15s 0700-2100
Request-rate: 1/9s 0900-1500
Request-rate: 1/1s 2200-0600
RENDERED
            ]
        ];
    }
}
