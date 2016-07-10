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
        $uriClient = new RobotsTxtParser\UriClient($base);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\UriClient', $uriClient);

        $this->assertTrue($uriClient->userAgent()->isAllowed('/'));
        $this->assertFalse($uriClient->userAgent()->isDisallowed('/'));

        $this->assertTrue($uriClient->userAgent()->isDisallowed('/blacklisted'));
        $this->assertFalse($uriClient->userAgent()->isAllowed('/blacklisted'));

        $this->assertTrue(count($uriClient->sitemap()->export()) > 0);

        $this->assertTrue(is_string($uriClient->host()->getWithFallback()));

        $txtClient = new RobotsTxtParser\TxtClient($uriClient->getBaseUri(), $uriClient->getStatusCode(), $uriClient->getContents(), $uriClient->getEncoding(), $uriClient->getEffectiveUri());
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $txtClient);

        $this->assertEquals($uriClient->render(), $txtClient->render());
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
