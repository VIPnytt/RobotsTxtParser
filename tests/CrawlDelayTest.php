<?php
namespace vipnytt\RobotsTxtParser\Tests;

use PHPUnit\Framework\TestCase;
use vipnytt\RobotsTxtParser;

/**
 * Class CrawlDelayTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class CrawlDelayTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testCrawlDelay($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertEquals(0, $parser->userAgent()->crawlDelay()->getValue());
        $this->assertEquals(0, $parser->userAgent('*')->crawlDelay()->getValue());
        $this->assertEquals(0.8, $parser->userAgent('GoogleBot')->crawlDelay()->getValue());
        $this->assertEquals(2.5, $parser->userAgent('BingBot')->crawlDelay()->getValue());
        $this->assertEquals(2.5, $parser->userAgent('BingBot')->requestRate()->getValue());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal());
            $this->testCrawlDelay($rendered, false);
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
User-Agent: GoogleBot
Crawl-delay: 0.8

User-Agent: BingBot
Crawl-delay: 2.5
ROBOTS
                ,
                <<<RENDERED
User-agent: bingbot
Crawl-delay: 2.5

User-agent: googlebot
Crawl-delay: 0.8
RENDERED
            ]
        ];
    }
}
