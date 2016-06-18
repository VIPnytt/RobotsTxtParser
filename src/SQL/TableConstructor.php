<?php
namespace vipnytt\RobotsTxtParser\SQL;

use PDO;
use vipnytt\RobotsTxtParser\Exceptions\SQLException;

/**
 * Class TableConstructor
 *
 * @package vipnytt\RobotsTxtParser\SQL
 */
class TableConstructor
{
    /**
     * Database connection
     * @var PDO
     */
    private $pdo;

    /**
     * Table name
     * @var string
     */
    private $table;

    /**
     * TableConstructor constructor.
     *
     * @param PDO $pdo
     * @param string $tableName
     */
    public function __construct(PDO $pdo, $tableName)
    {
        $this->pdo = $pdo;
        $this->table = $tableName;
    }

    /**
     * Create table
     *
     * @param string $sql
     * @param string $readme
     * @return bool
     * @throws SQLException
     */
    public function create($sql, $readme)
    {
        if ($this->exists()) {
            return true;
        }
        try {
            $this->pdo->query($sql);
        } catch (\PDOException $exception) {
        }
        if ($this->exists()) {
            return true;
        }
        throw new SQLException('Automatic setup failed, please create table `' . $this->table . '` manually. Setup instructions: ' . $readme);
    }

    /**
     * Check if the table exists
     *
     * @return bool
     */
    public function exists()
    {
        try {
            $result = $this->pdo->query("SELECT 1 FROM " . $this->table . " LIMIT 1;");
        } catch (\PDOException $e) {
            return false;
        }
        return $result !== false;
    }
}
