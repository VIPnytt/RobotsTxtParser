<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Client;

/**
 * Class CleanParamTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class CleanParamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $robotsTxtContent
     * @param array $result
     * @param string|false $rendered
     */
    public function testCleanParam($robotsTxtContent, $result, $rendered)
    {
        $parser = new Client('http://www.site1.com', 200, $robotsTxtContent);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent()->isDisallowed('http://www.site1.com/forums/showthread.php?s=681498b9648949605&ref=parent'));
        $this->assertFalse($parser->userAgent()->isAllowed('http://www.site1.com/forums/showthread.php?s=681498b9648949605&ref=parent'));

        $this->assertTrue($parser->userAgent()->isDisallowed('http://www.site1.com/page.php?ref=ads&uid=123456'));
        $this->assertFalse($parser->userAgent()->isAllowed('http://www.site1.com/page.php?ref=ads&uid=123456'));

        $this->assertEquals($result, $parser->getCleanParam());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render());
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
                <<<RENDERED
clean-param:abc /forum/showthread.php
clean-param:sid /forum/*.php
clean-param:sort /forum/*.php
clean-param:someTrash /
clean-param:otherTrash /
user-agent:*
disallow:clean-param:s /forum*/sh*wthread.php
disallow:clean-param:ref /forum*/sh*wthread.php
disallow:clean-param:uid /
RENDERED
            ]
        ];
    }
}
