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
     */
    public function testDownloadExample($base)
    {
        $parser = new RobotsTxtParser\URI($base);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\URI', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed("/"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/"));

        $this->assertTrue($parser->userAgent('*')->isAllowed("/"));
        $this->assertFalse($parser->userAgent('*')->isDisallowed("/"));

        $this->assertEquals([], $parser->sitemap()->export());

        $this->assertNull($parser->host()->get());

        $this->assertEquals([], $parser->cleanParam()->export());

        $this->assertEquals([], $parser->export());
        $this->assertEquals('', $parser->render());
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
                'http://example.com'
            ],
            [
                'http://www.example.com'
            ],
            [
                'https://example.com'
            ],
            [
                'https://www.example.com'
            ]
        ];
    }
}
