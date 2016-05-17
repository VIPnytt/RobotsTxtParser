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
     * @param string|false $rendered
     */
    public function testComment($robotsTxtContent, $result, $rendered)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertEquals($parser->userAgent('*')->getComments(), $result);
        $this->assertTrue($parser->userAgent('*')->isDisallowed("/"));
        $this->assertFalse($parser->userAgent('*')->isAllowed("/"));

        if ($rendered !== false) {
            $this->assertSame($rendered, $parser->render());
            $this->testComment($rendered, $result, false);
        }
    }

    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     */
    public function testCommentException($robotsTxtContent)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent('*')->isDisallowed("/"));
        $this->assertFalse($parser->userAgent('*')->isAllowed("/"));

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
Disallow: /
Comment: This comment should be sent back to the author/user of the robot.
Comment: Contact ceo@example.com for robot white listing.
ROBOTS
                ,
                [
                    'This comment should be sent back to the author/user of the robot.',
                    'Contact ceo@example.com for robot white listing.'
                ],
                <<<RENDERED
user-agent:*
comment:This comment should be sent back to the author/user of the robot.
comment:Contact ceo@example.com for robot white listing.
disallow:/
RENDERED
            ]
        ];
    }
}
