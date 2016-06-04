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
     * Empty rule test
     */
    public function testEmpty()
    {
        $parser = new RobotsTxtParser\Core('http://example.com', 200, '');
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Core', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed("/"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/"));

        $this->assertTrue($parser->userAgent('*')->isAllowed("/"));
        $this->assertFalse($parser->userAgent('*')->isDisallowed("/"));

        $this->assertEquals([], $parser->sitemap()->export());

        $this->assertNull($parser->host()->get());

        $this->assertEquals([], $parser->cleanParam()->export());

        $this->assertEquals([], $parser->export());
        $this->assertEquals('', $parser->render());
    }
}
