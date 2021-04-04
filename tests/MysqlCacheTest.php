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
use vipnytt\RobotsTxtParser\Exceptions\DatabaseException;

/**
 * Class MysqlCacheTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class MysqlCacheTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $uri
     * @param string $baseUri
     * @throws DatabaseException
     */
    public function testCache($uri, $baseUri)
    {
        try {
            $pdo = new \PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
        } catch (\PDOException $e) {
            $this->markTestSkipped('Unable to connect to the MySQL database');
            return;
        }
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $database = new RobotsTxtParser\Database($pdo);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Database', $database);

        $cache = $database->cache();
        $base = $cache->base($uri);
        $base->invalidate();

        $parser = $base->client();
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $parser);

        $debug = $base->debug();
        $this->assertTrue(count($debug, COUNT_NORMAL) >= 5);
        $this->assertEquals($debug['base'], $baseUri);
        $this->assertEquals($debug['content'], $parser->render()->compressed(PHP_EOL));

        $cache->cron();
        $cache->clean();

        $cache->cron(1, 255);
        $this->expectException(\InvalidArgumentException::class);
        $cache->cron(1, 999999);
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
            ],
            [
                'http://microsoft.com/robots.txt',
                'http://microsoft.com:80',
            ],
            [
                'http://example.com/',
                'http://example.com:80',
            ],
            [
                'ftp://mirror.ox.ac.uk/',
                'ftp://mirror.ox.ac.uk:21',
            ],
        ];
    }

    /**
     * @throws DatabaseException
     */
    public function testCacheSQLite()
    {
        try {
            $pdo = new \PDO('sqlite::memory:');
        } catch (\PDOException $e) {
            $this->markTestSkipped('Unable to connect to the SQLlite database');
            return;
        }
        $client = new RobotsTxtParser\Database($pdo);
        $this->expectException(DatabaseException::class);
        $client->cache();
    }
}
