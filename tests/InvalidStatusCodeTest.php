<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;
use vipnytt\RobotsTxtParser\Exceptions\StatusCodeException;

/**
 * Class InvalidStatusCodeTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class InvalidStatusCodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $base
     * @param int $statusCode
     * @param bool $isValid
     */
    public function testInvalidStatusCode($base, $statusCode, $isValid)
    {
        $parser = new RobotsTxtParser\TxtClient($base, $statusCode, '');
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        if (!$isValid) {
            $this->expectException(StatusCodeException::class);
        }
        $this->assertTrue($parser->userAgent()->isAllowed("/") || $parser->userAgent()->isDisallowed("/"));
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
                'http://example.com',
                99,
                false
            ],
            [
                'http://example.com',
                100,
                true
            ],
            [
                'http://example.com',
                500,
                true
            ],
            [
                'http://example.com',
                600,
                false
            ],
            [
                'ftp://example.com',
                100,
                true
            ],
            [
                'ftp://example.com',
                600,
                true
            ],
            [
                'ftp://example.com',
                1000,
                true
            ],
        ];
    }
}
