<?php
namespace vipnytt\RobotsTxtParser\Client\SQL;

use PDO;
use vipnytt\RobotsTxtParser\Client\SQL\Cache\CacheMaintenanceSQL;
use vipnytt\RobotsTxtParser\Client\SQL\Delay\DelayMaintenanceSQL;

/**
 * Class SQLMaintenance
 *
 * @package vipnytt\RobotsTxtParser\Client\SQL
 */
class SQLMaintenance
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
     * Cache
     *
     * @return CacheMaintenanceSQL
     */
    public function cache()
    {
        return new CacheMaintenanceSQL($this->pdo);
    }

    /**
     * Delay
     *
     * @return DelayMaintenanceSQL
     */
    public function delay()
    {
        return new DelayMaintenanceSQL($this->pdo);
    }
}
