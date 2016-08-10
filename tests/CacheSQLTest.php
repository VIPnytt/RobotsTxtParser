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
 * Class CacheSQLTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class CacheSQLTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $uri
     * @param string $base
     */
    public function testCacheSQL($uri, $base)
    {
        $pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

        $parser = new RobotsTxtParser\Cache($pdo);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Cache', $parser);
        $this->assertFalse($pdo->getAttribute(PDO::ATTR_ERRMODE) === PDO::ERRMODE_SILENT);

        $parser->invalidate($base);
        $client = $parser->client($uri);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $client);

        $debug = $parser->debug($uri);
        $this->assertTrue(count($debug, COUNT_NORMAL) >= 5);
        $this->assertEquals($debug['content'], $client->render()->compressed(PHP_EOL));

        for ($i = 1; $i <= 2; $i++) {
            $parser->client($uri);
        }

        $parser->cron();
        $parser->clean();

        $parser->cron(1, 255);
        $this->expectException(DatabaseException::class);
        $parser->cron(1, 999999);
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
            [
                'http://www.goldmansachs.com/robots.txt',
                'http://www.goldmansachs.com:80',
            ],
        ];
    }

    public function testCacheSQLite()
    {
        $pdo = new PDO('sqlite::memory:');
        $this->expectException(DatabaseException::class);
        new RobotsTxtParser\Cache($pdo);
    }
}
