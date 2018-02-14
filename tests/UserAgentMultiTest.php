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
 * Class UserAgentMultiTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class UserAgentMultiTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param string|false $rendered
     */
    public function testUserAgentMulti($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertEquals($rendered, $parser->render()->normal("\n"));
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
User-agent: agentA
Directive: unknown
User-agent: agentB
Disallow: /b
User-agent: agentC
User-agent: agentD
Disallow: /cd

User-agent: agentE

User-agent: agentF
Disallow: /f
ROBOTS
                ,
                <<<RENDERED
User-agent: agenta
User-agent: agente
Disallow:

User-agent: agentb
Disallow: /b

User-agent: agentc
User-agent: agentd
Disallow: /cd

User-agent: agentf
Disallow: /f
RENDERED
            ],
        ];
    }
}
