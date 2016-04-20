<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;
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
    public function testInvalidPath($robotsTxtContent)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->expectException(ClientException::class);
        $this->assertTrue($parser->userAgent()->isDisallowed('&&/1@|'));

        $this->expectException(ClientException::class);
        $this->assertFalse($parser->userAgent()->isAllowed('+£€@@1¤'));
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
