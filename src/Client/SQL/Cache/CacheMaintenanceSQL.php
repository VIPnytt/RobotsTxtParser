<?php
namespace vipnytt\RobotsTxtParser\Client\SQL\Cache;

use PDO;
use vipnytt\RobotsTxtParser\Client\SQL\SQLInterface;
use vipnytt\RobotsTxtParser\Client\SQL\SQLTrait;
use vipnytt\RobotsTxtParser\Exceptions\SQLException;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class CacheMaintenanceSQL
 *
 * @package vipnytt\RobotsTxtParser\Client\SQL\Cache
 */
class CacheMaintenanceSQL implements RobotsTxtInterface, SQLInterface
{
    use SQLTrait;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * Maintenance constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $this->pdoInitialize($pdo);
    }

    /**
     * Clean the cache table
     *
     * @param int $delay - in seconds
     * @return bool
     */
    public function clean($delay = self::CACHE_TIME)
    {
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__cache0
WHERE worker = 0 AND nextUpdate < (UNIX_TIMESTAMP() - :delay);
SQL
        );
        $query->bindParam(':delay', $delay, PDO::PARAM_INT);
        return $query->execute();
    }

    /**
     * Create SQL table
     *
     * @return bool
     * @throws SQLException
     */
    public function setup()
    {
        if (!$this->createTable($this->pdo, self::TABLE_CACHE, file_get_contents(__DIR__ . '/cache.sql'))) {
            throw new SQLException('Unable to create table! Please read instructions at ' . self::README_SQL_CACHE);
        }
        return true;
    }
}
