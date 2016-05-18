<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

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
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertEquals(0.5, $parser->userAgent()->getCacheDelay());
        $this->assertEquals(0.5, $parser->userAgent('*')->getCacheDelay());
        $this->assertEquals(8, $parser->userAgent('GoogleBot')->getCacheDelay());
        $this->assertEquals(9.2, $parser->userAgent('BingBot')->getCacheDelay());

        if ($rendered !== false) {
            $this->assertEquals(preg_replace('/\r\n|\r|\n/', "\r\n", $rendered), $parser->render());
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
cache-delay:8
crawl-delay:3.7
RENDERED
            ]
        ];
    }
}
