<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

/**
 * Class HostTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class HostTest extends \PHPUnit_Framework_TestCase
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

        $this->assertEquals('myhost.com', $parser->host()->get());
        $this->assertEquals('myhost.com', $parser->host()->getWithFallback());
        $this->assertFalse($parser->host()->isPreferred());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
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
host:myhost.com
user-agent:*
disallow:host:www.myhost.com
disallow:/cgi-bin
user-agent:yandex
disallow:/cgi-bin
RENDERED
            ]
        ];
    }
}
