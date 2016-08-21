<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use vipnytt\RobotsTxtParser;
use vipnytt\RobotsTxtParser\Exceptions\DatabaseException;

/**
 * Class DelaySQLTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class DelaySQLTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $uri
     * @param string $userAgent
     */
    public function testDelaySQL($uri, $userAgent)
    {
        $pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

        $parser = new RobotsTxtParser\UriClient($uri);
        $this->assertTrue(is_numeric($parser->userAgent($userAgent)->crawlDelay()->handle($pdo)->checkQueue()));
        $this->assertTrue(is_numeric($parser->userAgent($userAgent)->crawlDelay()->handle($pdo)->getTimeSleepUntil()));
        $this->assertTrue(is_numeric($parser->userAgent($userAgent)->crawlDelay()->handle($pdo)->sleep()));

        $delayHandler = new RobotsTxtParser\Delay($pdo);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Delay', $delayHandler);
        $this->assertFalse($pdo->getAttribute(PDO::ATTR_ERRMODE) === PDO::ERRMODE_SILENT);

        $client = $delayHandler->client($parser->userAgent($userAgent)->crawlDelay());
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Client\Delay\ClientInterface', $client);
        $this->assertTrue(is_numeric($client->getTimeSleepUntil()));

        $this->assertTrue(is_numeric($client->checkQueue()));
        $start = microtime(true);
        $sleepTime = $client->sleep();
        $stop = microtime(true);
        $this->assertTrue(
            $sleepTime >= ($stop - $start - 1) &&
            $sleepTime <= ($stop - $start + 1)
        );

        $this->assertTrue(is_array($delayHandler->getTopWaitTimes()));

        $client->reset();
        $this->assertTrue($client->getTimeSleepUntil() === 0);

        if ($parser->userAgent($userAgent)->crawlDelay()->getValue() > 0) {
            $client->reset(60);
            $queue = $client->checkQueue();
            $this->assertLessThanOrEqual(60, $queue);
            $this->assertGreaterThan(59, $queue);
            $debug = $delayHandler->debug($uri);
            $this->assertTrue(count($debug[strtolower($userAgent)], COUNT_NORMAL) >= 3);
        }

        $client->reset();
        $delayHandler->clean();
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
                'Test'
            ],
            [
                'http://microsoft.com/robots.txt',
                'Test'
            ],
            [
                'http://example.com/',
                'Test'
            ],
            [
                'http://www.vg.no/',
                'Test'
            ],
        ];
    }

    public function testDelaySQLite()
    {
        $pdo = new PDO('sqlite::memory:');
        $class = new RobotsTxtParser\Delay($pdo);
        $this->expectException(DatabaseException::class);
        $class->clean();
    }
}
