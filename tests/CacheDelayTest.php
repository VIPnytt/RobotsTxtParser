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
 * Class CacheDelayTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class CacheDelayTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testCacheDelay($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertEquals(0.5, $parser->userAgent()->cacheDelay()->getValue());
        $this->assertEquals(0.5, $parser->userAgent('*')->cacheDelay()->getValue());
        $this->assertEquals(8, $parser->userAgent('GoogleBot')->cacheDelay()->getValue());
        $this->assertEquals(9.2, $parser->userAgent('BingBot')->cacheDelay()->getValue());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal("\n"));
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
User-agent: *
Crawl-delay: 0.5

User-agent: bingbot
Cache-delay: 9.2

User-agent: googlebot
Crawl-delay: 3.7
Cache-delay: 8
RENDERED
            ]
        ];
    }
}
