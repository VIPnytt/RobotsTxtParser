<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

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
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent('*')->isDisallowed('/'));
        $this->assertFalse($parser->userAgent('*')->isAllowed('/'));
        $this->assertTrue($parser->userAgent()->isDisallowed('/'));
        $this->assertFalse($parser->userAgent()->isAllowed('/'));

        if ($rendered !== false) {
            $this->assertEquals(preg_replace('/\r\n|\r|\n/', "\r\n", $rendered), $parser->render());
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
