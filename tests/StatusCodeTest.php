<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser;

/**
 * Class StatusCodeTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class StatusCodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $base
     * @param int $statusCode
     * @param string $expectedResult
     */
    public function testStatusCode($base, $statusCode, $expectedResult)
    {
        $robots = <<<ROBOTS
User-agent: *
Disallow: /
Allow: /public/
ROBOTS;
        $parser = new RobotsTxtParser\Core($base, $statusCode, $robots);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Core', $parser);

        switch ($expectedResult) {
            case 'conditional':
                $this->assertTrue($parser->userAgent()->isDisallowed('/'));
                $this->assertFalse($parser->userAgent()->isAllowed('/'));
                $this->assertTrue($parser->userAgent()->isAllowed('/public/'));
                $this->assertFalse($parser->userAgent()->isDisallowed('/public/'));
                break;
            case 'full allow':
                $this->assertTrue($parser->userAgent()->isAllowed('/'));
                $this->assertFalse($parser->userAgent()->isDisallowed('/'));
                break;
            case 'full disallow':
                $this->assertTrue($parser->userAgent()->isDisallowed('/public/'));
                $this->assertFalse($parser->userAgent()->isAllowed('/public/'));
                break;
            default:
                $this->fail('Invalid test');
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
                'http://example.com',
                200,
                'conditional'
            ],
            [
                'http://example.com',
                301,
                'full allow'
            ],
            [
                'http://example.com',
                404,
                'full allow'
            ],
            [
                'http://example.com',
                503,
                'full disallow'
            ],
            [
                'http://example.com',
                522,
                'full allow'
            ],
            [
                'https://example.com',
                200,
                'conditional'
            ],
            [
                'https://example.com',
                301,
                'full allow'
            ],
            [
                'https://example.com',
                404,
                'full allow'
            ],
            [
                'https://example.com',
                503,
                'full disallow'
            ],
            [
                'ftp://example.com',
                200,
                'conditional'
            ],
            [
                'ftp://example.com',
                332,
                'conditional'
            ],
            [
                'ftp://example.com',
                425,
                'conditional'
            ],
            [
                'ftp://example.com',
                530,
                'conditional'
            ],
        ];
    }
}
