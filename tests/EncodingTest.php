<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

/**
 * Class EncodingTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class EncodingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $encoding
     */
    public function testEncoding($encoding)
    {
        // Invalid encodings are ignored, and the default encoding is used, without warning.
        // This test covers both mbstring and iconv
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, '', $encoding);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);
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
                'UTF9' //invalid
            ],
            [
                'ASCI' //invalid
            ],
            [
                'ISO8859' //invalid
            ],
            [
                'OSF10020402' //iconv
            ],
            [
                'UTF-16' //mbstring/iconv
            ],
        ];
    }
}
