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
 * Class CommentTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class CommentTest extends TestCase
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

        $this->assertEquals($parser->userAgent('*')->comment()->get(), []);

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal("\n"));
            $this->testComment($rendered, $result, false);
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
User-agent: *
Comment: This is a spam-like broadcast.

User-agent: receiver
Disallow: /
Comment: This comment should be sent back to the author/user of the robot 'receiver'.
Comment: Contact ceo@example.com for robot white listing.
RENDERED
            ]
        ];
    }
}
