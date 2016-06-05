<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

/**
 * Class RequestRateTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class RequestRateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param array $result
     * @param string|false $rendered
     */
    public function testRequestRate($robotsTxtContent, $result, $rendered)
    {
        $parser = new RobotsTxtParser\Basic('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Basic', $parser);

        $validRates = [];
        foreach ($result as $value) {
            $validRates[] = $value['rate'];
        }
        $this->assertTrue(in_array($parser->userAgent('Legacy')->requestRate()->get(), $validRates));
        $this->assertTrue(in_array($parser->userAgent('Legacy')->crawlDelay()->get(), $validRates));
        $this->assertTrue(in_array($parser->userAgent('Legacy')->cacheDelay()->get(), $validRates));

        if ($rendered !== false) {
            $this->assertEquals($result, $parser->userAgent('*')->requestRate()->export());
            $this->assertEquals($rendered, $parser->render());
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
Request-rate: 8/2m 07:00-21:00
Request-rate: 1200/3h 09.00-15.00
Request-rate: 9216/4d 07-21 # invalid time
Request-rate: 5 # invalid rate
ROBOTS
                ,
                [
                    [
                        'rate' => 1,
                        'from' => '2200',
                        'to' => '0600',
                    ],
                    [
                        'rate' => 15,
                        'from' => '0700',
                        'to' => '2100',
                    ],
                    [
                        'rate' => 9,
                        'from' => '0900',
                        'to' => '1500',
                    ],
                    [
                        'rate' => 37.5,
                    ],
                ],
                <<<RENDERED
user-agent:*
request-rate:1/15s 0700-2100
request-rate:1/1s 2200-0600
request-rate:1/37.5s
request-rate:1/9s 0900-1500
RENDERED
            ]
        ];
    }
}
