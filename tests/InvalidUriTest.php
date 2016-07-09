<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;
use vipnytt\RobotsTxtParser\Exceptions\ClientException;

/**
 * Class InvalidUriTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class InvalidUriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $base
     */
    public function testInvalidUri($base)
    {
        $this->expectException(ClientException::class);
        new RobotsTxtParser\UriClient($base);
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
                'shttp://example.com',
            ],
            [
                'htp://example.com',
            ],
            [
                'fttp://example.com',
            ],
            [
                'http://example.',
            ],
        ];
    }
}
