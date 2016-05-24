<?php
namespace vipnytt\RobotsTxtParser\Cache\SQL;

use PDO;
use vipnytt\RobotsTxtParser\Core\RobotsTxtInterface;

/**
 * Class Maintenance
 *
 * @package vipnytt\RobotsTxtParser\Cache\SQL
 */
class Maintenance implements RobotsTxtInterface
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * Maintenance constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Clean all tables automatically
     *
     * @return bool
     */
    public function autoClean()
    {
        $cache = $this->cleanCache();
        $delay = $this->cleanDelay();
        return $cache && $delay;
    }

    /**
     * Clean the cache table
     *
     * @param int $delay - in seconds
     * @return bool
     */
    public function cleanCache($delay = self::CACHE_TIME)
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
     * Clean the delay table
     *
     * @param int $delay - in seconds
     * @return bool
     */
    public function cleanDelay($delay = 60)
    {
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__delay0
WHERE microTime < ((UNIX_TIMESTAMP() - :delay) * 1000000);
SQL
        );
        $query->bindParam(':delay', $delay, PDO::PARAM_INT);
        return $query->execute();
    }
}
