<?php
namespace vipnytt\RobotsTxtParser\SQL;

use PDO;
use PDOException;

/**
 * Class SQLTrait
 *
 * @package vipnytt\RobotsTxtParser\SQL
 */
trait SQLTrait
{
    /**
     * Initialize PDO connection
     *
     * @param PDO $pdo |null
     * @return PDO
     */
    private function pdoInitialize(PDO $pdo = null)
    {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
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
        } catch (PDOException $e) {
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
        } catch (\Exception $e) {
            return FALSE;
        }
        return $result !== FALSE;
    }
}
