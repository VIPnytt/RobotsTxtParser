<?php
namespace vipnytt\RobotsTxtParser\Tests;

use PHPUnit\Framework\TestCase;
use vipnytt\RobotsTxtParser;

/**
 * Class CleanParamCommonTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class CleanParamCommonTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $uri
     * @param array $param
     * @param array $result
     */
    public function testCleanParamCommon($uri, $param, $result)
    {
        $parser = new RobotsTxtParser\TxtClient('http://www.example.com', 200, '');
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertEquals($result, $parser->cleanParam()->detectWithCommon($uri, $param));
    }

    /**
     * Generate test data
     *
     * @return array
     */
    public
    function generateDataForTest()
    {
        return [
            [
                'http://www.example.com/?ref=page1',
                [],
                [
                    'ref',
                ],
            ],
            [
                'http://www.example.com/?ref=search&source=google.com',
                [
                    'source'
                ],
                [
                    'ref',
                    'source',
                ],
            ],
            [
                'http://www.example.com/private/?token=a1b2c3&ref=/login',
                [],
                [
                    'ref',
                    'token',
                ],
            ],
            [
                'http://www.example.com/page4/?popup=1',
                [],
                [
                    'popup',
                ],
            ]
        ];
    }
}
