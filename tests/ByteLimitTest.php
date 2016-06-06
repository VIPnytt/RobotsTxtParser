<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

/**
 * Class ByteLimitTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class ByteLimitTest extends \PHPUnit_Framework_TestCase
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
        new RobotsTxtParser\Basic('http://example.com', 200, '', 'UTF-8', $byteLimit);
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
                15000,
                true,
            ],
            [
                20000,
                false,
            ],
        ];
    }
}
