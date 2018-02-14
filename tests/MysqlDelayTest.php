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
 * Class MysqlDelayTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class MysqlDelayTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $uri
     * @param string $userAgent
     * @throws RobotsTxtParser\Exceptions\DatabaseException
     */
    public function testDelay($uri, $userAgent)
    {
        $pdo = new \PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $handler = (new RobotsTxtParser\Database($pdo))->delay();
        $parser = new RobotsTxtParser\UriClient($uri);
        $this->assertTrue(is_numeric($parser->userAgent($userAgent)->crawlDelay()->handle($handler)->checkQueue()));
        $this->assertTrue(is_numeric($parser->userAgent($userAgent)->crawlDelay()->handle($handler)->getTimeSleepUntil()));
        $this->assertTrue(is_numeric($parser->userAgent($userAgent)->crawlDelay()->handle($handler)->sleep()));

        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Client\Delay\ManageInterface', $handler);
        $this->assertFalse($pdo->getAttribute(\PDO::ATTR_ERRMODE) === \PDO::ERRMODE_SILENT);

        $client = $handler->base($uri, $userAgent, $parser->userAgent($userAgent)->crawlDelay()->getValue());
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Client\Delay\BaseInterface', $client);
        $this->assertTrue(is_numeric($client->getTimeSleepUntil()));

        $this->assertTrue(is_numeric($client->checkQueue()));
        $start = microtime(true);
        $sleepTime = $client->sleep();
        $stop = microtime(true);
        $this->assertTrue(
            $sleepTime >= ($stop - $start - 1) &&
            $sleepTime <= ($stop - $start + 1)
        );

        $this->assertTrue(is_array($handler->getTopWaitTimes()));

        $client->reset();
        $this->assertTrue($client->getTimeSleepUntil() === 0);

        if ($parser->userAgent($userAgent)->crawlDelay()->getValue() > 0) {
            $client->reset(60);
            $queue = $client->checkQueue();
            $this->assertLessThanOrEqual(60, $queue);
            $this->assertGreaterThan(59, $queue);
            $this->assertTrue(count($client->debug(), COUNT_NORMAL) >= 3);
        }

        $client->reset();
        $handler->clean();
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
}
