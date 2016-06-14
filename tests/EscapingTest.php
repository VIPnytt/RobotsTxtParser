<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

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
     * @param string|false $rendered
     */
    public function testEscaping($robotsTxtContent, $rendered)
    {
        $parser = new RobotsTxtParser\TxtClient('http://example.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed("/%5C."));
        $this->assertFalse($parser->userAgent()->isDisallowed("/%5C."));

        /**
         * Additional tests to enable in the future, currently disabled due to bugs
         */
        //$this->assertTrue($parser->userAgent()->isDisallowed("/("));
        //$this->assertFalse($parser->userAgent()->isAllowed("/("));

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
            $this->testEscaping($rendered, false);
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
Disallow: /(
Disallow: /)
Disallow: /.
ROBOTS
                ,
                <<<RENDERED
user-agent:*
disallow:/(
disallow:/)
disallow:/.
RENDERED
            ]
        ];
    }
}
