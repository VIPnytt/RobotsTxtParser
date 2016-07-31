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
use vipnytt\RobotsTxtParser\Exceptions\ClientException;

/**
 * Class WrongRobotsTxtTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class WrongRobotsTxtTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $url
     */
    public function testWrongRobotsTxt($url)
    {
        $parser = new RobotsTxtParser\TxtClient('http://www.example.com', 200, '');
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

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
                'http://subdomain.example.com'
            ],
            [
                'http://ww.example.com'
            ]
        ];
    }
}
