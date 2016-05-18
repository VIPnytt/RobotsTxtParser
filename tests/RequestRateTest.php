<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

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
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertEquals($result, $parser->userAgent('*')->getRequestRates());

        $validRates = [];
        foreach ($result as $value) {
            $validRates[] = $value['rate'];
        }
        $this->assertTrue(in_array($parser->userAgent('Legacy')->getCrawlDelay(), $validRates));
        $this->assertTrue(in_array($parser->userAgent('Legacy')->getCacheDelay(), $validRates));

        if ($rendered !== false) {
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
Request-rate: 25/2m 07:00-21:00
Request-rate: 650/3h 09.00-15.00
Request-rate: 15750/4d 07-21 # invalid time
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
                        'rate' => 4.7999999999999998,
                        'from' => '0700',
                        'to' => '2100',
                    ],
                    [
                        'rate' => 16.615384615384617,
                        'from' => '0900',
                        'to' => '1500',
                    ],
                    [
                        'rate' => 21.942857142857143,
                    ],
                ],
                <<<RENDERED
user-agent:*
request-rate:1/1s 2200-0600
request-rate:1/4.8s 0700-2100
request-rate:1/16.615384615385s 0900-1500
request-rate:1/21.942857142857s
RENDERED
            ]
        ];
    }
}
