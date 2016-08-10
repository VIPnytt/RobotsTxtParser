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
 * Class StressTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class StressTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $base
     */
    public function testStress($base)
    {
        $parser = new RobotsTxtParser\UriClient($base);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\UriClient', $parser);

        $render1 = $parser->render()->compressed();
        $export1 = $parser->export();

        $import = new RobotsTxtParser\Import($export1, $base);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Import', $import);

        $render2 = $import->render()->compressed();
        $export2 = $import->export();

        $this->assertSame($render1, $render2);
        $this->assertSame($export1, $export2);
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
                'http://www.goldmansachs.com/robots.txt',
            ],
        ];
    }
}
