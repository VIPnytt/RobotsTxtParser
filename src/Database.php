<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser;

use vipnytt\RobotsTxtParser\Client\Cache;
use vipnytt\RobotsTxtParser\Client\Delay;
use vipnytt\RobotsTxtParser\Exceptions\DatabaseException;

/**
 * Class Database
 *
 * @package vipnytt\RobotsTxtParser
 */
class Database
{
    /**
     * MySQL driver name
     */
    const DRIVER_MYSQL = 'mysql';

    /**
     * Database handler
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string|null
     */
    private $driver;

    /**
     * @var Cache\ManageInterface|null
     */
    private $cache;

    /**
     * @var Delay\ManageInterface|null
     */
    private $delay;

    /**
     * Database constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $this->pdo->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_NATURAL);
        $this->pdo->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_NATURAL);
        $this->initialize();
    }

    /**
     * Driver special treatment
     *
     * @return int|bool
     */
    private function initialize()
    {
        switch ($this->driver) {
            case self::DRIVER_MYSQL:
                return $this->pdo->exec('SET NAMES utf8;');
        }
        return true;
    }

    /**
     * Cache manager
     *
     * @return Cache\ManageInterface
     * @throws DatabaseException
     */
    public function cache()
    {
        if ($this->cache !== null) {
            return $this->cache;
        }
        switch ($this->driver) {
            case self::DRIVER_MYSQL:
                return $this->cache = new Cache\MySQL\Manage($this->pdo);
        }
        throw new DatabaseException('Unsupported PDO driver.');
    }

    /**
     * Delay manager
     *
     * @return Delay\ManageInterface
     * @throws DatabaseException
     */
    public function delay()
    {
        if ($this->delay !== null) {
            return $this->delay;
        }
        switch ($this->driver) {
            case self::DRIVER_MYSQL:
                return $this->delay = new Delay\MySQL\Manage($this->pdo);
        }
        throw new DatabaseException('Unsupported PDO driver.');
    }
}
