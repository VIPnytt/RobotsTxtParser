<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

/**
 * Class DisallowAllTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class DisallowAllTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testDisallowAll($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent('*')->isDisallowed('/'));
        $this->assertFalse($parser->userAgent('*')->isAllowed('/'));
        $this->assertTrue($parser->userAgent()->isDisallowed('/'));
        $this->assertFalse($parser->userAgent()->isAllowed('/'));

        $this->assertTrue($parser->userAgent()->isAllowed("/robots.txt"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/robots.txt"));
        $this->assertTrue($parser->userAgent('*')->isAllowed("http://example.com/robots.txt"));
        $this->assertFalse($parser->userAgent('*')->isDisallowed("http://example.com/robots.txt"));

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
            $this->testDisallowAll($rendered, false);
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
User-agent: *
Disallow: /
ROBOTS
                ,
                <<<RENDERED
user-agent:*
disallow:/
RENDERED
            ]
        ];
    }
}
