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
 * Class DisallowAllTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class DisallowAllTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testDisallowAll($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent('*')->isDisallowed('/'));
        $this->assertFalse($parser->userAgent('*')->isAllowed('/'));
        $this->assertTrue($parser->userAgent()->isDisallowed('/'));
        $this->assertFalse($parser->userAgent()->isAllowed('/'));

        $this->assertTrue($parser->userAgent()->isAllowed("/robots.txt"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/robots.txt"));
        $this->assertTrue($parser->userAgent('*')->isAllowed("http://example.com/robots.txt"));
        $this->assertFalse($parser->userAgent('*')->isDisallowed("http://example.com/robots.txt"));

        $this->assertTrue($parser->userAgent()->isDisallowed('/admin/'));
        $this->assertFalse($parser->userAgent()->isAllowed('/admin/'));

        $this->assertTrue($parser->userAgent()->isDisallowed('/page/test/'));
        $this->assertFalse($parser->userAgent()->isAllowed('/page/test/'));

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal("\n"));
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
Disallow: *test*
Disallow: /admin/
Disallow: /
ROBOTS
                ,
                <<<RENDERED
User-agent: *
Disallow: /
RENDERED
            ]
        ];
    }
}
