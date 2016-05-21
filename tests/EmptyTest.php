<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class EmptyTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class EmptyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Empty rule test
     */
    public function testEmpty()
    {
        $parser = new Client('http://example.com', 200, '');
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed("/"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/"));

        $this->assertTrue($parser->userAgent('*')->isAllowed("/"));
        $this->assertFalse($parser->userAgent('*')->isDisallowed("/"));

        $this->assertEquals([], $parser->getSitemaps());

        $this->assertNull($parser->getHost());

        $this->assertEquals([], $parser->getCleanParam());

        $this->assertEquals([], $parser->export());
        $this->assertEquals('', $parser->render());
    }
}
