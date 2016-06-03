<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

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
        // Invalid encodings are ignored, and the default encoding is used, without warning.
        $parser = new RobotsTxtParser\Core('http://example.com', 200, '', $encoding);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Core', $parser);
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
