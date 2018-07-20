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
     * @throws RobotsTxtParser\Exceptions\ClientException
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

        $this->assertTrue($parser->userAgent('DenyMe')->isDisallowed('http://example.com/deny/'));
        $this->assertFalse($parser->userAgent('DenyMe')->isAllowed('http://example.com/deny/'));

        $this->assertTrue($parser->userAgent('DenyMe')->isAllowed('http://example.com/deny/allow/'));
        $this->assertFalse($parser->userAgent('DenyMe')->isDisallowed('http://example.com/deny/allow/'));

        $this->assertTrue($parser->userAgent('DenyUs')->isDisallowed('http://example.com/deny/'));
        $this->assertFalse($parser->userAgent('DenyUs')->isAllowed('http://example.com/deny/'));

        $this->assertTrue($parser->userAgent('DenyUs')->isAllowed('http://example.com/deny/allow/'));
        $this->assertFalse($parser->userAgent('DenyUs')->isDisallowed('http://example.com/deny/allow/'));

        $this->assertTrue($parser->userAgent('ImageBot')->isDisallowed('/image.jpg'));
        $this->assertFalse($parser->userAgent('ImageBot')->isAllowed('/image.jpg'));

        $this->assertTrue($parser->userAgent('ImageBot')->isDisallowed('http://example.com/image.jpg'));
        $this->assertFalse($parser->userAgent('ImageBot')->isAllowed('http://example.com/image.jpg'));

        $this->assertTrue($parser->userAgent('ImageBot')->isDisallowed('http://example.com/foo/bar/image.jpg'));
        $this->assertFalse($parser->userAgent('ImageBot')->isAllowed('http://example.com/foo/bar/image.jpg'));

        if ($rendered !== false) {
            if (version_compare(phpversion(), '7.0.0', '<')) {
                $this->markTestIncomplete('Sort algorithm changed as of PHP 7');
            }
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
Noindex: $
Disallow: /*
Allow: /$

User-Agent: DenyMe
Disallow: /deny/$$

User-Agent: DenyUs
Disallow: /*deny/$
Disallow: deny/$

User-Agent: ImageBot
Disallow: *.jpg$
ROBOTS
                ,
                <<<RENDERED
User-agent: *
Disallow: /
Allow: /$

User-agent: denyme
Disallow: /deny/$

User-agent: denyus
Disallow: /*deny/$

User-agent: imagebot
Disallow: /*.jpg$
RENDERED
            ]
        ];
    }
}
