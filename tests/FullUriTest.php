<?php
namespace vipnytt\RobotsTxtParser\Tests;

use PHPUnit\Framework\TestCase;
use vipnytt\RobotsTxtParser;

/**
 * Class FullUriTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class FullUriTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testFullUri($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent()->isDisallowed("http://example.com/admin/"));
        $this->assertFalse($parser->userAgent()->isAllowed("http://example.com/admin/"));
        $this->assertTrue($parser->userAgent('*')->isDisallowed("HTTP://EXAMPLE.COM/admin/"));
        $this->assertFalse($parser->userAgent('*')->isAllowed("HTTP://EXAMPLE.COM/admin/"));

        $this->assertTrue($parser->userAgent()->isAllowed("http://example.com/"));
        $this->assertFalse($parser->userAgent()->isDisallowed("http://example.com/"));
        $this->assertTrue($parser->userAgent('*')->isAllowed("http://example.com/"));
        $this->assertFalse($parser->userAgent('*')->isDisallowed("http://example.com/"));

        $this->assertFalse($parser->userAgent('badbot')->isAllowed("http://example.com/"));
        $this->assertTrue($parser->userAgent('badbot')->isDisallowed("http://example.com/"));

        $this->assertTrue($parser->host()->isPreferred());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal());
            $this->testFullUri($rendered, false);
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

User-agent: BadBot
Disallow: /
Host: example.com
ROBOTS
                ,
                <<<RENDERED
Host: example.com

User-agent: *
Disallow: /admin/

User-agent: badbot
Disallow: /
RENDERED
            ]
        ];
    }
}
