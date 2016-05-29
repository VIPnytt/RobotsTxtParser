<?php
namespace vipnytt\RobotsTxtParser\Client\SQL\Delay;

use PDO;
use vipnytt\RobotsTxtParser\Client\SQL\SQLInterface;
use vipnytt\RobotsTxtParser\Client\SQL\SQLTrait;
use vipnytt\RobotsTxtParser\Exceptions\SQLException;

/**
 * Class DelayMaintenanceSQL
 *
 * @package vipnytt\RobotsTxtParser\Client\SQL\Delay
 */
class DelayMaintenanceSQL implements SQLInterface
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
     * Clean the delay table
     *
     * @return bool
     */
    public function clean()
    {
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__delay0
WHERE microTime < (UNIX_TIMESTAMP() * 1000000);
SQL
        );
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
        if (!$this->createTable($this->pdo, self::TABLE_DELAY, file_get_contents(__DIR__ . '/delay.sql'))) {
            throw new SQLException('Unable to create table! Please read instructions at ' . self::README_SQL_DELAY);
        }
        return true;
    }
}
