<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class EndAnchorWildcardTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class EndAnchorWildcardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     */
    public function testEndAnchorWildcard($robotsTxtContent)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent()->isDisallowed('http://example.com/deny_all/'));
        $this->assertFalse($parser->userAgent()->isAllowed('http://example.com/deny_all/'));
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
Disallow: *deny_all/$
ROBOTS
            ]
        ];
    }
}
