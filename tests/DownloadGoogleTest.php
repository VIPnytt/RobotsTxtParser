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

/**
 * Class DownloadGoogleTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class DownloadGoogleTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $base
     */
    public function testDownloadGoogle($base)
    {
        $uriClient = new RobotsTxtParser\UriClient($base);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\UriClient', $uriClient);

        $this->assertFalse($uriClient->userAgent()->isAllowed('/search'));
        $this->assertTrue($uriClient->userAgent()->isDisallowed('/search'));

        $this->assertTrue($uriClient->userAgent()->isAllowed('/search/about'));
        $this->assertFalse($uriClient->userAgent()->isDisallowed('/search/about'));

        $this->assertTrue(count($uriClient->sitemap()->export()) > 0);

        $this->assertIsString($uriClient->host()->getWithUriFallback());

        $txtClient = new RobotsTxtParser\TxtClient($uriClient->getBaseUri(), $uriClient->getStatusCode(), $uriClient->getContents(), $uriClient->getEncoding(), $uriClient->getEffectiveUri());
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $txtClient);

        $this->assertEquals($uriClient->render()->normal("\n"), $txtClient->render()->normal("\n"));
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
