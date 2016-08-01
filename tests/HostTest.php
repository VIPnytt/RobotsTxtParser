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
 * Class HostTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class HostTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testHost($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://www.myhost.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent()->isDisallowed('http://www.myhost.com/'));
        $this->assertFalse($parser->userAgent()->isAllowed('http://www.myhost.com/'));
        $this->assertTrue($parser->userAgent()->isDisallowed('/'));
        $this->assertFalse($parser->userAgent()->isAllowed('/'));

        $this->assertEquals('myhost.com', $parser->host()->export());
        $this->assertEquals('myhost.com', $parser->host()->getWithUriFallback());
        $this->assertFalse($parser->host()->isPreferred());

        $this->assertFalse($parser->userAgent()->allow()->host()->isListed('http://www.myhost.com/'));
        $this->assertTrue($parser->userAgent()->disallow()->host()->isListed('http://www.myhost.com/'));
        $this->assertFalse($parser->userAgent()->noIndex()->host()->isListed('http://www.myhost.com/'));

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal("\n"));
            $this->testHost($rendered, false);
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
Disallow: /cgi-bin
Disallow: Host: www.myhost.com

User-agent: Yandex
Disallow: /cgi-bin

# Examples of Host directives that will be ignored
Host: www.myhost-.com
Host: www.-myhost.com
Host: www.myhost.com:100000
Host: www.my_host.com
Host: .my-host.com:8000
Host: my-host.com.Host: my..host.com
Host: www.myhost.com:8080/
Host: 213.180.194.129
Host: http://213.180.194.129:80
Host: [2001:db8::1]
Host: FE80::0202:B3FF:FE1E:8329
Host: https://[2001:db8:0:1]:80
Host: www.firsthost.com,www.secondhost.com
Host: www.firsthost.com www.secondhost.com

# Examples of valid Host directives
Host: myhost.com # uses this one
Host: www.myhost.com # is not used
ROBOTS
                ,
                <<<RENDERED
Host: myhost.com

User-agent: *
Disallow: Host: www.myhost.com
Disallow: /cgi-bin

User-agent: yandex
Disallow: /cgi-bin
RENDERED
            ]
        ];
    }

    public function testHostWithFallback()
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, '', 'UTF-8', 'https://example.com');
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);
        $this->assertEquals('https://example.com', $parser->host()->getWithUriFallback());

        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, '', 'UTF-8', 'http://example.com:8080');
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);
        $this->assertEquals('http://example.com:8080', $parser->host()->getWithUriFallback());
    }
}
