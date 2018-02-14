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
 * Class InvalidPathTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class InvalidPathTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     */
    public function testInvalidPathAllowed($robotsTxtContent)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->expectException(\InvalidArgumentException::class);
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

        $this->expectException(\InvalidArgumentException::class);
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
