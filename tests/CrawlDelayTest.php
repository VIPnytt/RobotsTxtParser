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
     */
    public function testCrawlDelay($robotsTxtContent)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertEquals(0, $parser->userAgent()->getCrawlDelay());
        $this->assertEquals(0, $parser->userAgent('*')->getCrawlDelay());
        $this->assertEquals(0.8, $parser->userAgent('GoogleBot')->getCrawlDelay());
        $this->assertEquals(2.5, $parser->userAgent('BingBot')->getCrawlDelay());
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
            ]
        ];
    }
}
