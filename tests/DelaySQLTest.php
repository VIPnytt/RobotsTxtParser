<?php
namespace vipnytt\RobotsTxtParser\Tests;

use PDO;
use vipnytt\RobotsTxtParser;

/**
 * Class DelaySQLTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class DelaySQLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $uri
     * @param string $base
     * @param string $userAgent
     */
    public function testDelaySQL($uri, $base, $userAgent)
    {
        $pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);

        $parser = new RobotsTxtParser\UriClient($uri);

        $delayHandler = new RobotsTxtParser\DelayHandler($pdo);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\DelayHandler', $delayHandler);

        $client = $delayHandler->client($parser->userAgent($userAgent)->crawlDelay());
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Client\Directives\DelayHandlerClient', $client);

        $microTime = $client->getTimeSleepUntil();

        $query = $pdo->prepare(<<<SQL
SELECT *
FROM robotstxt__delay0
WHERE base = :base AND userAgent = :userAgent;
SQL
        );
        $query->bindParam(':base', $base);
        $query->bindParam(':userAgent', $userAgent);
        $query->execute();
        $row = $query->fetch();

        if ($microTime !== 0) {
            $this->assertEquals($microTime * 1000000, $row['microTime']);
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
                'http://google.com',
                'http://google.com:80',
                'Test'
            ],
            [
                'http://microsoft.com/robots.txt',
                'http://microsoft.com:80',
                'Test'
            ],
            [
                'http://example.com/',
                'http://example.com:80',
                'Test'
            ],
        ];
    }
}
