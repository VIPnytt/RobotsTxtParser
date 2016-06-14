<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;
use vipnytt\RobotsTxtParser\Exceptions\ClientException;

/**
 * Class InvalidPathTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class InvalidPathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     */
    public function testInvalidPathAllowed($robotsTxtContent)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->expectException(ClientException::class);
        $this->assertFalse($parser->userAgent()->isAllowed('+£€@@1¤'));
    }

    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     */
    public function testInvalidPathDisallowed($robotsTxtContent)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->expectException(ClientException::class);
        $this->assertTrue($parser->userAgent()->isDisallowed('&&/1@|'));
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
                <<<ROBOTS
User-agent: *
Disallow: /
ROBOTS
            ]
        ];
    }
}
