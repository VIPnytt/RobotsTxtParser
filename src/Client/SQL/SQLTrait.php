<?php
namespace vipnytt\RobotsTxtParser\Client\SQL;

use PDO;
use PDOException;

/**
 * Class SQLTrait
 *
 * @package vipnytt\RobotsTxtParser\Client\SQL
 */
trait SQLTrait
{
    /**
     * Initialize PDO connection
     *
     * @param PDO $pdo
     * @return PDO
     */
    private function pdoInitialize(PDO $pdo)
    {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        return $pdo;
    }

    /**
     * Create table
     *
     * @param PDO $pdo
     * @param string $table
     * @param string $query
     * @return bool
     */
    private function createTable(PDO $pdo, $table, $query)
    {
        if ($this->tableExists($pdo, $table)) {
            return true;
        }
        try {
            $pdo->query($query);
        } catch (PDOException $Exception) {
            return FALSE;
        }
        return $this->tableExists($pdo, $table);
    }

    /**
     * Check if the table exists
     *
     * @param PDO $pdo
     * @param string $table
     * @return bool
     */
    private function tableExists(PDO $pdo, $table)
    {
        try {
            $result = $pdo->query("SELECT 1 FROM $table LIMIT 1;");
        } catch (PDOException $Exception) {
            return FALSE;
        }
        return $result !== FALSE;
    }
}
