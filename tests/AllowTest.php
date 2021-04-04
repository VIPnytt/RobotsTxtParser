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
 * Class AllowTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class AllowTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testDisAllow($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed("/"));
        $this->assertTrue($parser->userAgent()->isAllowed("/article"));
        $this->assertTrue($parser->userAgent()->isDisallowed("/temp"));
        $this->assertTrue($parser->userAgent()->isDisallowed("/Admin"));
        $this->assertTrue($parser->userAgent()->isDisallowed("/admin"));
        $this->assertTrue($parser->userAgent()->isDisallowed("/admin/cp/test/"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/article"));
        $this->assertFalse($parser->userAgent()->isAllowed("/temp"));
        $this->assertFalse($parser->userAgent()->isAllowed("/Admin"));
        $this->assertFalse($parser->userAgent()->isAllowed("/admin"));
        $this->assertFalse($parser->userAgent()->isAllowed("/admin/cp/test/"));

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
        $this->assertTrue($parser->userAgent('agentV')->isAllowed("/Foo"));
        $this->assertTrue($parser->userAgent('agentV')->isAllowed("/public/foo"));

        $this->assertTrue($parser->userAgent('agentW')->isDisallowed("/foo"));
        $this->assertTrue($parser->userAgent('agentW')->isAllowed("/bar"));
        $this->assertTrue($parser->userAgent('agentW')->isAllowed("/Foo"));
        $this->assertTrue($parser->userAgent('agentW')->isAllowed("/public/foo"));

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
            if (version_compare(phpversion(), '7.0.0', '<')) {
                $this->markTestIncomplete('Sort algorithm changed as of PHP 7');
            }
            $this->assertEquals($rendered, $parser->render()->normal("\n"));
            $this->testDisAllow($rendered, false);
        }
    }

    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testDisAllowIsListed($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);
        if (version_compare(phpversion(), '7.0.0', '<')) {
            $this->markTestIncomplete('Sort algorithm changed as of PHP 7');
        }
        $this->assertEquals($rendered, $parser->render()->normal("\n"));

        // Expected result: String length of matching rule
        $this->assertEquals(6, $parser->userAgent('*')->disallow()->hasPath('/admin'));
        $this->assertEquals(4, $parser->userAgent('agentV')->allow()->hasPath('/bar'));

        $this->assertEquals('/admin', $parser->userAgent('*')->disallow()->isCovered('/admin'));
        $this->assertEquals('/bar', $parser->userAgent('agentV')->allow()->isCovered('/bar'));

        $this->expectException(\InvalidArgumentException::class);
        $parser->userAgent('*')->disallow()->isCovered('http;//www.example.com/invalid');
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
User-agent: anyone
User-agent: *
Disallow: /admin
Disallow: /admin
Disallow: /Admin*
Disallow: /temp#comment
Disallow: /forum**
Disallow: /admin/cp/test/

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
Allow: &&/1@| #invalid
Allow: /forum/$
Allow: /article

User-agent: crawlerZ
Disallow:
Disallow: /
Allow: /$
ROBOTS
                ,
                <<<RENDERED
User-agent: *
User-agent: anyone
Disallow: /temp
Disallow: /forum
Disallow: /admin
Disallow: /Admin
Disallow: /admin/cp/test/

User-agent: agentv
User-agent: agentw
Disallow: /foo
Allow: /bar

User-agent: boty
Disallow: /
Allow: /forum/$
Allow: /article

User-agent: crawlerz
Disallow: /
Allow: /$

User-agent: spiderx
Disallow: /admin
Disallow: /assets
RENDERED
            ]
        ];
    }
}
