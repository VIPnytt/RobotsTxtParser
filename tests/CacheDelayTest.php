<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Parser;

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
     */
    public function testCacheDelay($robotsTxtContent)
    {
        $parser = new Parser('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertEquals(0.5, $parser->userAgent()->getCacheDelay());
        $this->assertEquals(0.5, $parser->userAgent('*')->getCacheDelay());
        $this->assertEquals(8, $parser->userAgent('GoogleBot')->getCacheDelay());
        $this->assertEquals(9.2, $parser->userAgent('BingBot')->getCacheDelay());
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
Crawl-Delay: 4
Cache-Delay: 9.2
ROBOTS
            ]
        ];
    }
}
