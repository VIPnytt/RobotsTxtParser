<?php
namespace vipnytt\RobotsTxtParser\Tests;

use PHPUnit\Framework\TestCase;
use vipnytt\RobotsTxtParser;

/**
 * Class FTPTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class FTPTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $base
     * @param string $host
     */
    public function testFTP($base, $host)
    {
        $uriClient = new RobotsTxtParser\UriClient($base);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\UriClient', $uriClient);

        $this->assertEquals($host, $uriClient->host()->getWithUriFallback());

        $txtClient = new RobotsTxtParser\TxtClient($uriClient->getBaseUri(), $uriClient->getStatusCode(), $uriClient->getContents(), $uriClient->getEncoding(), $uriClient->getEffectiveUri());
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $txtClient);

        $this->assertEquals($uriClient->render()->normal(), $txtClient->render()->normal());
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
                'ftp://MIRROR.ox.ac.uk',
                'mirror.ox.ac.uk'
            ],
            [
                'ftp://MIRRORS.easynews.com',
                'mirrors.easynews.com',
            ],
            [
                'ftps://MIRRORS.easynews.com',
                'mirrors.easynews.com',
            ],
            [
                'sftp://MIRRORS.easynews.com',
                'mirrors.easynews.com',
            ],
        ];
    }
}
