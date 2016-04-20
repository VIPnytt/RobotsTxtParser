<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class DownloadGoogleTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class DownloadGoogleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $base
     */
    public function testDownloadGoogle($base)
    {
        $parser = new Client($base);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent()->isDisallowed('/search'));
        $this->assertFalse($parser->userAgent()->isAllowed('/search'));

        $this->assertTrue($parser->userAgent()->isAllowed('/search/about'));
        $this->assertFalse($parser->userAgent()->isDisallowed('/search/about'));

        $this->assertTrue(count($parser->getSitemaps()) > 0);
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
                'http://google.com'
            ],
            [
                'http://www.google.com'
            ],
            [
                'https://google.com'
            ],
            [
                'https://www.google.com'
            ]
        ];
    }
}
