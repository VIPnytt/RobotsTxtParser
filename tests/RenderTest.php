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
 * Class RenderTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class RenderTest extends TestCase
{
    public function testInvalidNewLine()
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, '');
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->expectException(\InvalidArgumentException::class);
        $parser->render()->normal('<br>');
    }

    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testRender($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertEquals($rendered['compressed'], $parser->render()->compressed("\n"));
        $this->assertEquals($rendered['normal'], $parser->render()->normal("\n"));
        $this->assertEquals($rendered['compatibility'], $parser->render()->compatibility("\n"));

        // Make sure the compatibility robots.txt has a newline at the end
        foreach (["\r", "\n", "\r\n"] as $separator) {
            $length = strlen($separator);
            $this->assertEquals($separator, substr($parser->render()->compatibility($separator), -$length));
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
Allow: /public
Noindex: /private
Crawl-delay: 5
Cache-delay: 10
Request-rate: 1200/3h 09.00-15.00
Request-rate: 1000/1h
Visit-time: 01.23-23.01
Robot-version 2.0
Comment: Please honor the robots.txt rules. Thanks!
User-agent: Yahoo! slurp
User-agent: Bingbot
Disallow: /
User-agent: DuckDuckGo
Disallow: /
Host: example.com
Sitemap: http://example.com/sitemap.xml
Sitemap: HTTP://EXAMPLE.COM/sitemap.xml.gz
ROBOTS
                ,
                [
                    'compressed' => <<<COMPRESSED
host:example.com
sitemap:http://example.com/sitemap.xml
sitemap:http://example.com/sitemap.xml.gz
user-agent:*
visit-time:0123-2301
noindex:/private
disallow:/admin/
allow:/public
crawl-delay:5
cache-delay:10
request-rate:1/9s 0900-1500
request-rate:5/18s
comment:Please honor the robots.txt rules. Thanks!
user-agent:bingbot
user-agent:duckduckgo
user-agent:yahoo! slurp
disallow:/
COMPRESSED
                    ,
                    'normal' => <<<NORMAL
Host: example.com

Sitemap: http://example.com/sitemap.xml
Sitemap: http://example.com/sitemap.xml.gz

User-agent: *
Visit-time: 0123-2301
Noindex: /private
Disallow: /admin/
Allow: /public
Crawl-delay: 5
Cache-delay: 10
Request-rate: 1/9s 0900-1500
Request-rate: 5/18s
Comment: Please honor the robots.txt rules. Thanks!

User-agent: bingbot
User-agent: duckduckgo
User-agent: yahoo! slurp
Disallow: /
NORMAL
                    ,
                    'compatibility' => <<<COMPATIBILITY
User-agent: yahoo! slurp
Disallow: /

User-agent: duckduckgo
Disallow: /

User-agent: bingbot
Disallow: /

User-agent: *
Visit-time: 0123-2301
Noindex: /private
Disallow: /admin/
Allow: /public
Crawl-delay: 5
Cache-delay: 10
Request-rate: 1/9s 0900-1500
Request-rate: 5/18s
Comment: Please honor the robots.txt rules. Thanks!

Host: example.com

Sitemap: http://example.com/sitemap.xml
Sitemap: http://example.com/sitemap.xml.gz

COMPATIBILITY
                    ,
                ]
            ]
        ];
    }
}
