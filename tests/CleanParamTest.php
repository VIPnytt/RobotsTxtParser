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

        $this->assertEquals(['ref', 's'], $parser->cleanParam()->detect('http://www.site1.com/forums/showthread.php?s=681498b9648949605&ref=parent&popup=0'));
        $this->assertEquals(['popup', 'ref', 's'], $parser->cleanParam()->detectWithCommon('http://www.site1.com/forums/showthread.php?s=681498b9648949605&ref=parent&popup=0'));

        $this->assertEquals($result['Clean-param'], $parser->cleanParam()->export());

        if ($rendered !== false) {
            $this->assertEquals($rendered, $parser->render()->normal("\n"));
            $this->testCleanParam($rendered, $result, false);
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
Clean-param: s&ref /forum*/sh*wthread.php**
Clean-param: uid /
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
                        "uid" => [
                            "/",
                        ],
                        "s" => [
                            "/forum*/sh*wthread.php",
                        ],
                        "ref" => [
                            "/forum*/sh*wthread.php",
                        ],
                    ],
                    'NoIndex' => [],
                    'Disallow' => [],
                    'Allow' => [],
                ],
                <<<RENDERED
Clean-param: abc /forum/showthread.php
Clean-param: otherTrash&someTrash&uid /
Clean-param: ref&s /forum*/sh*wthread.php
Clean-param: sid&sort /forum/*.php
RENDERED
            ]
        ];
    }
}
