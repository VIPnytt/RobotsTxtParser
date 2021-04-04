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

class SqliteTest extends TestCase
{
    /**
     * @throws RobotsTxtParser\Exceptions\DatabaseException
     */
    public function testDelay()
    {
        try {
            $pdo = new \PDO('sqlite::memory:');
        } catch (\PDOException $e) {
            $this->markTestSkipped('Unable to connect to the SQLite database');
            return;
        }
        $client = new RobotsTxtParser\Database($pdo);
        $this->expectException(RobotsTxtParser\Exceptions\DatabaseException::class);
        $client->delay();
    }
}
