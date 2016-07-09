<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

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
        $parser = new RobotsTxtParser\UriClient($base);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\UriClient', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed('/'));
        $this->assertFalse($parser->userAgent()->isDisallowed('/'));

        $this->assertTrue($parser->userAgent()->isDisallowed('/blacklisted'));
        $this->assertFalse($parser->userAgent()->isAllowed('/blacklisted'));

        $this->assertTrue(count($parser->sitemap()->export()) > 0);

        $this->assertTrue(is_string($parser->host()->getWithFallback()));
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
