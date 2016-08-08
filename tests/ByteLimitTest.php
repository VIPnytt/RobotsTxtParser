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
     * @param bool $warning
     */
    public function testByteLimitException($byteLimit, $warning)
    {
        if ($warning) {
            $this->expectException(RobotsTxtParser\Exceptions\ClientException::class);
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
                false,
            ],
            [
                24000,
                true,
            ],
            [
                25000,
                false,
            ],
        ];
    }
}
