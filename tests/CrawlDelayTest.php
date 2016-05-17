<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class CrawlDelayTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class CrawlDelayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testCrawlDelay($robotsTxtContent, $rendered)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertEquals(0, $parser->userAgent()->getCrawlDelay());
        $this->assertEquals(0, $parser->userAgent('*')->getCrawlDelay());
        $this->assertEquals(0.8, $parser->userAgent('GoogleBot')->getCrawlDelay());
        $this->assertEquals(2.5, $parser->userAgent('BingBot')->getCrawlDelay());
        $this->assertEquals(864, $parser->userAgent('Legacy')->getCrawlDelay());

        if ($rendered !== false) {
            $this->assertSame($rendered, $parser->render());
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

User-Agent: Legacy
Request-rate: 100/24h
ROBOTS
                ,
                <<<RENDERED
user-agent:bingbot
crawl-delay:2.5
user-agent:googlebot
crawl-delay:0.8
user-agent:legacy
request-rate:1/864s
RENDERED
            ]
        ];
    }
}
