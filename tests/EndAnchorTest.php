<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class EndAnchorTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class EndAnchorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testEndAnchor($robotsTxtContent, $rendered)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed('/'));
        $this->assertFalse($parser->userAgent()->isDisallowed('/'));

        $this->assertTrue($parser->userAgent()->isDisallowed('/asd'));
        $this->assertFalse($parser->userAgent()->isAllowed('/asd'));

        $this->assertTrue($parser->userAgent()->isDisallowed('/asd/'));
        $this->assertFalse($parser->userAgent()->isAllowed('/asd/'));

        $this->assertTrue($parser->userAgent('DenyMe')->isDisallowed('http://example.com/deny_all/'));
        $this->assertFalse($parser->userAgent('DenyMe')->isAllowed('http://example.com/deny_all/'));

        if ($rendered !== false) {
            $this->assertSame($rendered, $parser->render());
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
user-agent:*
allow:/$
disallow:/*
user-agent:denyme
disallow:/deny_all/$
disallow:*deny_all/$
disallow:deny_all/$
RENDERED
            ]
        ];
    }
}
