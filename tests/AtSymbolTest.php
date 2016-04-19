<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Parser;

/**
 * Class AtSymbolTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class AtSymbolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     */
    public function testAtSymbol($robotsTxtContent)
    {
        $parser = new Parser('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed("/peanuts"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/peanuts"));

        $this->assertTrue($parser->userAgent()->isDisallowed("/url_containing_@_symbol"));
        $this->assertFalse($parser->userAgent()->isAllowed("/url_containing_@_symbol"));
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
User-Agent: *
Disallow: /url_containing_@_symbol
Allow: /peanuts
ROBOTS
            ]
        ];
    }
}
