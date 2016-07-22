<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

/**
 * Class DownloadExampleTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class DownloadExampleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $base
     * @param array $result
     */
    public function testDownloadExample($base, $result)
    {
        $parser = new RobotsTxtParser\UriClient($base);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\UriClient', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed("/"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/"));

        $this->assertTrue($parser->userAgent('*')->isAllowed("/"));
        $this->assertFalse($parser->userAgent('*')->isDisallowed("/"));

        $this->assertEquals([], $parser->sitemap()->export());

        $this->assertNull($parser->host()->export());
        $this->assertEquals(parse_url($base, PHP_URL_HOST), $parser->host()->getWithUriFallback());

        $this->assertEquals([], $parser->cleanParam()->export());

        $this->assertEquals($result, $parser->export());
        $this->assertEquals('', $parser->render());
    }

    /**
     * Generate test data
     *
     * @return array
     */
    public function generateDataForTest()
    {
        $array = [
            'host' => null,
            'clean-param' => [],
            'sitemap' => [],
            'user-agent' => [],
        ];
        return [
            [
                'http://example.com',
                $array
            ],
            [
                'http://www.example.com',
                $array
            ],
            [
                'https://example.com',
                $array
            ],
            [
                'https://www.example.com',
                $array
            ],
            [
                'http://127.0.0.1',
                $array
            ],
            [
                'http://[::1]/',
                $array
            ],
        ];
    }
}
