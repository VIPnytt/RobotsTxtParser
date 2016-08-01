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
 * Class EndAnchorTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class EndAnchorTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testEndAnchor($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed('/'));
        $this->assertFalse($parser->userAgent()->isDisallowed('/'));

        $this->assertTrue($parser->userAgent()->isDisallowed('/asd'));
        $this->assertFalse($parser->userAgent()->isAllowed('/asd'));

        $this->assertTrue($parser->userAgent()->isDisallowed('/asd/'));
        $this->assertFalse($parser->userAgent()->isAllowed('/asd/'));

        $this->assertTrue($parser->userAgent('DenyMe')->isDisallowed('http://example.com/deny_all/'));
        $this->assertFalse($parser->userAgent('DenyMe')->isAllowed('http://example.com/deny_all/'));

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal("\n"));
            $this->testEndAnchor($rendered, false);
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
User-Agent: *
Disallow: /*
Allow: /$

User-Agent: DenyMe
Disallow: /deny_all/$
Disallow: *deny_all/$
Disallow: deny_all/$
ROBOTS
                ,
                <<<RENDERED
User-agent: *
Disallow: /*
Allow: /$

User-agent: denyme
Disallow: *deny_all/$
Disallow: /deny_all/$
RENDERED
            ]
        ];
    }
}
