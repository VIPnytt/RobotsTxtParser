<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Parser;

/**
 * Class FullUrlTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class FullUrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     */
    public function testFullUrl($robotsTxtContent)
    {
        $parser = new Parser('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent()->isDisallowed("http://example.com/admin/"));
        $this->assertFalse($parser->userAgent()->isAllowed("http://example.com/admin/"));
        $this->assertTrue($parser->userAgent('*')->isDisallowed("http://example.com/admin/"));
        $this->assertFalse($parser->userAgent('*')->isAllowed("http://example.com/admin/"));

        $this->assertTrue($parser->userAgent()->isAllowed("http://example.com/"));
        $this->assertFalse($parser->userAgent()->isDisallowed("http://example.com/"));
        $this->assertTrue($parser->userAgent('*')->isAllowed("http://example.com/"));
        $this->assertFalse($parser->userAgent('*')->isDisallowed("http://example.com/"));

        $this->assertFalse($parser->userAgent('badbot')->isAllowed("http://example.com/"));
        $this->assertTrue($parser->userAgent('badbot')->isDisallowed("http://example.com/"));
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
Disallow: /admin/

User-agent: BadBot
Disallow: /
ROBOTS
            ]
        ];
    }
}
