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
 * Class IgnoreTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class IgnoreTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testIgnore($robotsTxtContent, $rendered = '')
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent('*')->isAllowed('/tech'));
        $this->assertFalse($parser->userAgent('*')->isDisallowed('/tech'));
        $this->assertTrue($parser->userAgent()->isAllowed('/tech'));
        $this->assertFalse($parser->userAgent()->isDisallowed('/tech'));

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal());
            $this->testIgnore($rendered, false);
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
#Disallow: /tech
ROBOTS
            ],
            [
                <<<ROBOTS
User-agent: *
Disallow: #/tech
ROBOTS
            ],
            [
                <<<ROBOTS
User-agent: *
Disal#low: /tech
ROBOTS
            ],
            [
                <<<ROBOTS
User-agent: *
Disallow#: /tech # ds
ROBOTS
            ]
        ];
    }
}
