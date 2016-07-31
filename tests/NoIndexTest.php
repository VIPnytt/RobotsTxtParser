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
 * Class NoIndexTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class NoIndexTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testNoIndex($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent('*')->noIndex()->isListed('/public/'));

        $this->assertTrue($parser->userAgent('*')->isDisallowed('/public/'));
        $this->assertFalse($parser->userAgent('*')->isAllowed('/public/'));
        $this->assertTrue($parser->userAgent()->isDisallowed('/public/'));
        $this->assertFalse($parser->userAgent()->isAllowed('/public/'));

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal());
            $this->testNoIndex($rendered, false);
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
Allow: /public/
NoIndex: /
ROBOTS
                ,
                <<<RENDERED
User-agent: *
Noindex: /
Disallow: /admin/
Allow: /public/
RENDERED
            ]
        ];
    }
}
