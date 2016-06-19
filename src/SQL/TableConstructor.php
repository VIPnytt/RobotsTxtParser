<?php
namespace vipnytt\RobotsTxtParser\SQL;

use PDO;
use vipnytt\RobotsTxtParser\Exceptions\SQLException;

/**
 * Class TableConstructor
 *
 * @package vipnytt\RobotsTxtParser\SQL
 */
class TableConstructor implements SQLInterface
{
    /**
     * Table white list
     */
    const TABLE_WHITE_LIST = [
        self::TABLE_CACHE,
        self::TABLE_DELAY,
    ];

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
        $this->table = in_array($tableName, self::TABLE_WHITE_LIST, true) ? $tableName : '';
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
        } catch (\Exception $exception) {
            // Query failed
        }
        if ($this->exists()) {
            return true;
        }
        throw new SQLException('Missing table `' . $this->table . '`. Setup instructions: ' . $readme);
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
        } catch (\Exception $e) {
            return false;
        }
        return $result !== false;
    }
}
