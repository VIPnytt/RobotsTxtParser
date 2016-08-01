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
 * Class WhitespaceTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class WhitespaceTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testWhitespace($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent('*')->isDisallowed('/admin'));
        $this->assertFalse($parser->userAgent('*')->isAllowed('/admin'));
        $this->assertTrue($parser->userAgent()->isDisallowed('/admin'));
        $this->assertFalse($parser->userAgent()->isAllowed('/admin'));

        $this->assertTrue($parser->userAgent('*')->isAllowed('/admin/front'));
        $this->assertFalse($parser->userAgent('*')->isDisallowed('/admin/front'));
        $this->assertTrue($parser->userAgent()->isAllowed('/admin/front'));
        $this->assertFalse($parser->userAgent()->isDisallowed('/admin/front'));

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal("\n"));
            $this->testWhitespace($rendered, false);
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
User-agent      :*
Disallow : /admin
Allow    :   /admin/front
ROBOTS
                ,
                <<<RENDERED
User-agent: *
Disallow: /admin
Allow: /admin/front
RENDERED
            ]
        ];
    }
}
