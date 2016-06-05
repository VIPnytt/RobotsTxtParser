<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

/**
 * Class CacheDelayTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class CacheDelayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testCacheDelay($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\Basic('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Basic', $parser);

        $this->assertEquals(0.5, $parser->userAgent()->cacheDelay()->get());
        $this->assertEquals(0.5, $parser->userAgent('*')->cacheDelay()->get());
        $this->assertEquals(8, $parser->userAgent('GoogleBot')->cacheDelay()->get());
        $this->assertEquals(9.2, $parser->userAgent('BingBot')->cacheDelay()->get());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
            $this->testCacheDelay($rendered, false);
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
Crawl-Delay: 0.5

User-Agent: GoogleBot
Crawl-Delay: 3.7
Cache-Delay: 8

User-Agent: BingBot
Crawl-Delay: 0
Cache-Delay: 9.2
Cache-Delay: 2.9
ROBOTS
                ,
                <<<RENDERED
user-agent:*
crawl-delay:0.5
user-agent:bingbot
cache-delay:9.2
user-agent:googlebot
crawl-delay:3.7
cache-delay:8
RENDERED
            ]
        ];
    }
}
