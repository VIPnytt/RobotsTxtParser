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
 * Class ByteLimitTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class ByteLimitTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param int|null $byteLimit
     */
    public function testByteLimitException($byteLimit)
    {
        if ($byteLimit !== null) {
            // PHPUnit 7: Switch to \PHPUnit\Framework\Constraint\IsType::TYPE_INT
            $this->assertIsInt($byteLimit);
            if (24 * 1024 > $byteLimit) {
                // Less than 24 kilobytes
                $this->expectException(\InvalidArgumentException::class);
            }
        } else {
            $this->expectNotToPerformAssertions();
        }
        new RobotsTxtParser\TxtClient('http://example.com', 200, '', 'UTF-8', 'http://example.com', $byteLimit);
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
                null,
            ],
            [
                24000,
            ],
            [
                25000,
            ],
        ];
    }
}
