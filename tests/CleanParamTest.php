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
 * Class CleanParamTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class CleanParamTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param array $result
     * @param string|false $rendered
     */
    public function testCleanParam($robotsTxtContent, $result, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://www.site1.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent()->isDisallowed('http://www.site1.com/forums/showthread.php?s=681498b9648949605&ref=parent'));
        $this->assertFalse($parser->userAgent()->isAllowed('http://www.site1.com/forums/showthread.php?s=681498b9648949605&ref=parent'));

        $this->assertTrue($parser->userAgent()->isDisallowed('http://www.site1.com/page.php?ref=ads&uid=123456'));
        $this->assertFalse($parser->userAgent()->isAllowed('http://www.site1.com/page.php?ref=ads&uid=123456'));

        $this->assertEquals($result['Clean-param'], $parser->cleanParam()->export());

        $this->assertEquals($result['NoIndex'], $parser->userAgent()->noIndex()->cleanParam()->export());
        $this->assertEquals($result['Disallow'], $parser->userAgent()->disallow()->cleanParam()->export());
        $this->assertEquals($result['Allow'], $parser->userAgent()->allow()->cleanParam()->export());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal());
            $this->testCleanParam($rendered, $result, false);
        }
    }

    /**
     * Generate test data
     *
     * @return array
     */
    public
    function generateDataForTest()
    {
        return [
            [
                <<<ROBOTS
User-agent: *
Disallow: Clean-param: s&ref /forum*/sh*wthread.php
Disallow: Clean-param: uid /
Clean-param: abc /forum/showthread.php
Clean-param: sid&sort /forum/*.php
Clean-param: someTrash&otherTrash
ROBOTS
                ,
                [
                    'Clean-param' => [
                        "abc" => [
                            "/forum/showthread.php",
                        ],
                        "sid" => [
                            "/forum/*.php",
                        ],
                        "sort" => [
                            "/forum/*.php",
                        ],
                        "someTrash" => [
                            "/",
                        ],
                        "otherTrash" => [
                            "/",
                        ],
                    ],
                    'NoIndex' => [],
                    'Disallow' => [
                        'ref' => [
                            '/forum*/sh*wthread.php',
                        ],
                        's' => [
                            '/forum*/sh*wthread.php',
                        ],
                        'uid' => [
                            '/',
                        ]
                    ],
                    'Allow' => [],
                ],
                <<<RENDERED
Clean-param: abc /forum/showthread.php
Clean-param: otherTrash&someTrash /
Clean-param: sid&sort /forum/*.php

User-agent: *
Disallow: Clean-param: ref&s /forum*/sh*wthread.php
Disallow: Clean-param: uid /
RENDERED
            ]
        ];
    }
}
