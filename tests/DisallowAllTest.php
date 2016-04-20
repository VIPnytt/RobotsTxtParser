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
     */
    public function testDisallowAll($robotsTxtContent)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent('*')->isDisallowed('/'));
        $this->assertFalse($parser->userAgent('*')->isAllowed('/'));
        $this->assertTrue($parser->userAgent()->isDisallowed('/'));
        $this->assertFalse($parser->userAgent()->isAllowed('/'));
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
            ]
        ];
    }
}
