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
 * Class StatusCodeTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class StatusCodeTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     */
    public function testStatusCode($robotsTxtContent)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 300, $robotsTxtContent);
        $this->assertTrue($parser->userAgent('*')->isAllowed("/"));

        $parser = new RobotsTxtParser\TxtClient('http://example.com', 400, $robotsTxtContent);
        $this->assertTrue($parser->userAgent('*')->isAllowed("/"));

        $parser = new RobotsTxtParser\TxtClient('http://example.com', 500, $robotsTxtContent);
        $this->assertTrue($parser->userAgent('*')->isDisallowed("/"));
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
Noindex: /
ROBOTS
            ],
            [
                <<<ROBOTS
User-agent: *
Disallow: /
ROBOTS
            ],
            [
                <<<ROBOTS
User-agent: *
Allow: /
ROBOTS
            ],
        ];
    }
}
