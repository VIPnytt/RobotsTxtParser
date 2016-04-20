<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class DownloadMicrosoftTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class DownloadMicrosoftTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $base
     */
    public function testDownloadMicrosoft($base)
    {
        $parser = new Client($base);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed('/'));
        $this->assertFalse($parser->userAgent()->isDisallowed('/'));

        $this->assertTrue($parser->userAgent()->isDisallowed('/blacklisted'));
        $this->assertFalse($parser->userAgent()->isAllowed('/blacklisted'));

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
                'http://microsoft.com'
            ],
            [
                'http://www.microsoft.com'
            ],
            [
                'https://microsoft.com'
            ],
            [
                'https://www.microsoft.com'
            ]
        ];
    }
}
