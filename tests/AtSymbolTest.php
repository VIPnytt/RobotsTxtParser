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
 * Class AtSymbolTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class AtSymbolTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testAtSymbol($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed("/peanuts"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/peanuts"));

        $this->assertTrue($parser->userAgent()->isDisallowed("/url_containing_@_symbol"));
        $this->assertFalse($parser->userAgent()->isAllowed("/url_containing_@_symbol"));

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal());
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
User-agent: *
Disallow: /url_containing_@_symbol
Allow: /peanuts
RENDERED
            ]
        ];
    }
}
