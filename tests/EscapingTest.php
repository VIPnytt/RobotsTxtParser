<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class EscapingTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class EscapingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     */
    public function testEscaping($robotsTxtContent)
    {
        $parser = new Client('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed("/%5C."));
        $this->assertFalse($parser->userAgent()->isDisallowed("/%5C."));

        /**
         * Additional tests to enable in the future, currently disabled due to bugs
         */
        //$this->assertTrue($parser->userAgent()->isDisallowed("/("));
        //$this->assertFalse($parser->userAgent()->isAllowed("/("));
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
Disallow: /(
Disallow: /)
Disallow: /.
ROBOTS
            ]
        ];
    }
}
