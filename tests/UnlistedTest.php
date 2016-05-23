<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

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
     * @param string|false $rendered
     */
    public function testUnlisted($robotsTxtContent, $rendered)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Client', $parser);

        $this->assertTrue($parser->userAgent('*')->isAllowed('/path/'));
        $this->assertFalse($parser->userAgent('*')->isDisallowed('/path/'));
        $this->assertTrue($parser->userAgent()->isAllowed('/path/'));
        $this->assertFalse($parser->userAgent()->isDisallowed('/path/'));

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
            $this->testUnlisted($rendered, false);
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
Disallow: /admin/
Allow: /public/
ROBOTS
                ,
                <<<RENDERED
user-agent:*
allow:/public/
disallow:/admin/
RENDERED
            ]
        ];
    }
}
