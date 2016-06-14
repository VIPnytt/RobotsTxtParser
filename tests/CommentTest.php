<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

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
     * @param string|false $rendered
     */
    public function testComment($robotsTxtContent, $result, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertEquals($parser->userAgent('receiver')->comment()->export(), $result);
        $this->assertTrue($parser->userAgent('receiver')->isDisallowed("/"));
        $this->assertFalse($parser->userAgent('receiver')->isAllowed("/"));

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
            $this->testComment($rendered, $result, false);
        }
    }

    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     */
    public function testCommentException($robotsTxtContent)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent('receiver')->isDisallowed("/"));
        $this->assertFalse($parser->userAgent('receiver')->isAllowed("/"));

        // Comments not exported, and is therefore thrown as E_USER_NOTICE.
        $this->expectException(\PHPUnit_Framework_Error_Notice::class);
        $parser = null;
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
Comment: This is a spam-like broadcast.
User-agent: receiver
Disallow: /
Comment: This comment should be sent back to the author/user of the robot 'receiver'.
Comment: Contact ceo@example.com for robot white listing.
ROBOTS
                ,
                [
                    "This comment should be sent back to the author/user of the robot 'receiver'.",
                    "Contact ceo@example.com for robot white listing."
                ],
                <<<RENDERED
user-agent:*
comment:This is a spam-like broadcast.
user-agent:receiver
disallow:/
comment:This comment should be sent back to the author/user of the robot 'receiver'.
comment:Contact ceo@example.com for robot white listing.
RENDERED
            ]
        ];
    }
}
