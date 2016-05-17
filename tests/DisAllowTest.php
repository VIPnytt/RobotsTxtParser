<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class DisAllowTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class DisAllowTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testDisAllowTest($robotsTxtContent, $rendered)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed("/"));
        $this->assertTrue($parser->userAgent()->isAllowed("/article"));
        $this->assertTrue($parser->userAgent()->isDisallowed("/temp"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/article"));
        $this->assertFalse($parser->userAgent()->isAllowed("/temp"));

        $this->assertTrue($parser->userAgent('*')->isAllowed("/"));
        $this->assertTrue($parser->userAgent('*')->isAllowed("/article"));
        $this->assertTrue($parser->userAgent('*')->isDisallowed("/temp"));
        $this->assertFalse($parser->userAgent('*')->isDisallowed("/"));
        $this->assertFalse($parser->userAgent('*')->isDisallowed("/article"));
        $this->assertFalse($parser->userAgent('*')->isAllowed("/temp"));

        $this->assertTrue($parser->userAgent('notListed')->isAllowed("/"));
        $this->assertTrue($parser->userAgent('notListed')->isAllowed("/article"));
        $this->assertTrue($parser->userAgent('notListed')->isDisallowed("/temp"));
        $this->assertFalse($parser->userAgent('notListed')->isDisallowed("/"));
        $this->assertFalse($parser->userAgent('notListed')->isDisallowed("/article"));
        $this->assertFalse($parser->userAgent('notListed')->isAllowed("/temp"));

        $this->assertTrue($parser->userAgent('agentV')->isDisallowed("/foo"));
        $this->assertTrue($parser->userAgent('agentV')->isAllowed("/bar"));
        $this->assertTrue($parser->userAgent('agentW')->isDisallowed("/foo"));
        $this->assertTrue($parser->userAgent('agentW')->isAllowed("/bar"));

        $this->assertTrue($parser->userAgent('spiderX/1.0')->isAllowed("/temp"));
        $this->assertTrue($parser->userAgent('spiderX/1.0')->isDisallowed("/assets"));
        $this->assertTrue($parser->userAgent('spiderX/1.0')->isAllowed("/forum"));
        $this->assertFalse($parser->userAgent('spiderX/1.0')->isDisallowed("/temp"));
        $this->assertFalse($parser->userAgent('spiderX/1.0')->isAllowed("/assets"));
        $this->assertFalse($parser->userAgent('spiderX/1.0')->isDisallowed("/forum"));

        $this->assertTrue($parser->userAgent('botY-test')->isDisallowed("/"));
        $this->assertTrue($parser->userAgent('botY-test')->isDisallowed("/forum"));
        $this->assertTrue($parser->userAgent('botY-test')->isAllowed("/forum/"));
        $this->assertTrue($parser->userAgent('botY-test')->isDisallowed("/forum/topic"));
        $this->assertTrue($parser->userAgent('botY-test')->isDisallowed("/public"));
        $this->assertFalse($parser->userAgent('botY-test')->isAllowed("/"));
        $this->assertFalse($parser->userAgent('botY-test')->isAllowed("/forum"));
        $this->assertFalse($parser->userAgent('botY-test')->isDisallowed("/forum/"));
        $this->assertFalse($parser->userAgent('botY-test')->isAllowed("/forum/topic"));
        $this->assertFalse($parser->userAgent('botY-test')->isAllowed("/public"));

        $this->assertTrue($parser->userAgent('crawlerZ')->isAllowed("/"));
        $this->assertTrue($parser->userAgent('crawlerZ')->isDisallowed("/forum"));
        $this->assertTrue($parser->userAgent('crawlerZ')->isDisallowed("/public"));
        $this->assertFalse($parser->userAgent('crawlerZ')->isDisallowed("/"));
        $this->assertFalse($parser->userAgent('crawlerZ')->isAllowed("/forum"));
        $this->assertFalse($parser->userAgent('crawlerZ')->isAllowed("/public"));

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
            $this->testDisAllowTest($rendered, false);
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
Disallow: /admin
Disallow: /temp#comment
Disallow: /forum
Disallow: /admin

User-agent: agentV
User-agent: agentW
Disallow: /foo
Allow: /bar #comment

User-agent: spiderX
Disallow:
Disallow: /admin#
Disallow: /assets

User-agent: botY
Disallow: /
Disallow: &&/1@| #invalid
Allow: /forum/$
Allow: /article

User-agent: crawlerZ
Disallow:
Disallow: /
Allow: /$
ROBOTS
                ,
                <<<RENDERED
user-agent:*
disallow:/admin
disallow:/temp
disallow:/forum
user-agent:agentv
allow:/bar
disallow:/foo
user-agent:agentw
allow:/bar
disallow:/foo
user-agent:boty
allow:/forum/$
allow:/article
disallow:/
disallow:&&/1@|
user-agent:crawlerz
allow:/$
disallow:/
user-agent:spiderx
disallow:/admin
disallow:/assets
RENDERED
            ]
        ];
    }
}
