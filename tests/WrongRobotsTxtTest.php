<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;
use vipnytt\RobotsTxtParser\Exceptions\ClientException;

/**
 * Class WrongRobotsTxtTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class WrongRobotsTxtTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $url
     */
    public function testWrongRobotsTxt($url)
    {
        $parser = new Client('http://www.example.com', 200, '');
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->expectException(ClientException::class);
        $parser->userAgent()->isAllowed($url);
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
                'http://example.com'
            ],
            [
                'http://www.example.net'
            ],
            [
                'http://subdomain.example.comh'
            ],
            [
                'http://ww.example.com'
            ]
        ];
    }
}
