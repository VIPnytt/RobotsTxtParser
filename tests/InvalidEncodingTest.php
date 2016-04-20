<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;
use vipnytt\RobotsTxtParser\Exceptions\ParserException;

/**
 * Class InvalidEncodingTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class InvalidEncodingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $encoding
     */
    public function testInvalidEncoding($encoding)
    {
        $this->expectException(ParserException::class);
        new Client('http://example.com', 200, '', $encoding);
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
                'UTF9'
            ],
            [
                'ASCI'
            ],
            [
                'ISO8859'
            ]
        ];
    }
}
