<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

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
        $uriClient = new RobotsTxtParser\UriClient($base);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\UriClient', $uriClient);

        $this->assertTrue($uriClient->userAgent()->isDisallowed('/search'));
        $this->assertFalse($uriClient->userAgent()->isAllowed('/search'));

        $this->assertTrue($uriClient->userAgent()->isAllowed('/search/about'));
        $this->assertFalse($uriClient->userAgent()->isDisallowed('/search/about'));

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
