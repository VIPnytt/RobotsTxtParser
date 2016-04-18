<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Parser;

/**
 * Class UnlistedTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class UnlistedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     */
    public function testUnlisted($robotsTxtContent)
    {
        $parser = new Parser('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent('*')->isAllowed('/path/'));
        $this->assertFalse($parser->userAgent('*')->isDisallowed('/path/'));
        $this->assertTrue($parser->userAgent()->isAllowed('/path/'));
        $this->assertFalse($parser->userAgent()->isDisallowed('/path/'));
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
Allow: /public/
ROBOTS
            ]
        ];
    }
}
