<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class CommentTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class CommentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param array $result
     */
    public function testCommentFeedback($robotsTxtContent, $result)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent('*')->isDisallowed("/"));
        $this->assertFalse($parser->userAgent('*')->isAllowed("/"));

        $this->expectException('Exception');
        $parser = null;

        /**
         * Test 2
         */
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertEquals($parser->userAgent('*')->getComments(), $result);
        $this->assertTrue($parser->userAgent('*')->isDisallowed("/"));
        $this->assertFalse($parser->userAgent('*')->isAllowed("/"));
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
Comment: This comment should be sent back to the author/user of the robot.
Comment: Contact ceo@example.com for robot white listing.
ROBOTS
                ,
                [
                    'This comment should be sent back to the author/user of the robot.',
                    'Contact ceo@example.com for robot white listing.'
                ]
            ]
        ];
    }
}
