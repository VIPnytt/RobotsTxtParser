<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class WhitespaceTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class WhitespaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testWhitespace($robotsTxtContent, $rendered)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent('*')->isDisallowed('/admin'));
        $this->assertFalse($parser->userAgent('*')->isAllowed('/admin'));
        $this->assertTrue($parser->userAgent()->isDisallowed('/admin'));
        $this->assertFalse($parser->userAgent()->isAllowed('/admin'));

        $this->assertTrue($parser->userAgent('*')->isAllowed('/admin/front'));
        $this->assertFalse($parser->userAgent('*')->isDisallowed('/admin/front'));
        $this->assertTrue($parser->userAgent()->isAllowed('/admin/front'));
        $this->assertFalse($parser->userAgent()->isDisallowed('/admin/front'));

        if ($rendered !== false) {
            $this->assertSame($rendered, $parser->render());
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
user-agent:*
allow:/admin/front
disallow:/admin
RENDERED
            ]
        ];
    }
}
