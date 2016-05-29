<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

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
     * @param string|false $rendered
     */
    public function testAtSymbol($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\Input('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Input', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed("/peanuts"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/peanuts"));

        $this->assertTrue($parser->userAgent()->isDisallowed("/url_containing_@_symbol"));
        $this->assertFalse($parser->userAgent()->isAllowed("/url_containing_@_symbol"));

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
            $this->testAtSymbol($rendered, false);
        }
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
                ,
                <<<RENDERED
user-agent:*
allow:/peanuts
disallow:/url_containing_@_symbol
RENDERED
            ]
        ];
    }
}
