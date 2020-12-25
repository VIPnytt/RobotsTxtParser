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
 * Class DownloadMicrosoftTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class DownloadMicrosoftTest extends TestCase
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

        $this->assertInternalType('string', $uriClient->host()->getWithUriFallback());

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
