<?php
namespace vipnytt\RobotsTxtParser\Tests;

use PDO;
use vipnytt\RobotsTxtParser;
use vipnytt\RobotsTxtParser\Exceptions\DatabaseException;

/**
 * Class CacheSQLTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class CacheSQLTest extends \PHPUnit_Framework_TestCase
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

        $query = $pdo->prepare(<<<SQL
SELECT *
FROM robotstxt__cache1
WHERE base = :base;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_STR);
        $query->execute();
        $row = $query->fetch();
        $this->assertEquals($client->render(), $row['content']);

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
                'http://www.vg.no/',
                'http://www.vg.no:80',
            ],
            [
                'ftp://mirror.ox.ac.uk/',
                'ftp://mirror.ox.ac.uk:21',
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
