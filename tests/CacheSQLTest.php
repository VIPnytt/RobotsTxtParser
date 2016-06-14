<?php
namespace vipnytt\RobotsTxtParser\Tests;

use PDO;
use vipnytt\RobotsTxtParser;

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

        $parser = new RobotsTxtParser\CacheHandler($pdo);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\CacheHandler', $parser);
        $parser->setup();

        $client = $parser->client($uri);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\TxtClient', $client);

        $query = $pdo->prepare(<<<SQL
SELECT *
FROM robotstxt__cache0
WHERE base = :base;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_STR);
        $query->execute();
        $row = $query->fetch();
        $this->assertEquals($client->render(), $row['content']);

        $parser->cron();
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
        ];
    }
}
