<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

/**
 * Class EmptyTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class EmptyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param array $result
     */
    public function testEmpty($result)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, '');
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed("/"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/"));

        $this->assertTrue($parser->userAgent('*')->isAllowed("/"));
        $this->assertFalse($parser->userAgent('*')->isDisallowed("/"));

        $this->assertEquals([], $parser->sitemap()->export());

        $this->assertNull($parser->host()->get());

        $this->assertEquals([], $parser->cleanParam()->export());

        $this->assertEquals($result, $parser->export());
        $this->assertEquals('', $parser->render());
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
                [
                    'host' => null,
                    'clean-param' => [],
                    'sitemap' => [],
                    'user-agent' =>
                        [
                            '*' =>
                                [
                                    'robot-version' => null,
                                    'visit-time' => [],
                                    'disallow' =>
                                        [
                                            'host' => [],
                                            'path' => [],
                                            'clean-param' => [],
                                        ],
                                    'allow' =>
                                        [
                                            'host' => [],
                                            'path' => [],
                                            'clean-param' => [],
                                        ],
                                    'crawl-delay' => null,
                                    'cache-delay' => null,
                                    'request-rate' => [],
                                    'comment' => [],
                                ],
                        ],
                ],
            ]
        ];
    }
}
