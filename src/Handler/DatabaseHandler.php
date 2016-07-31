<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Handler;

use PDO;
use vipnytt\RobotsTxtParser\Client;
use vipnytt\RobotsTxtParser\Exceptions\DatabaseException;
use vipnytt\UserAgentParser;

/**
 * Class DatabaseHandler
 *
 * @package vipnytt\RobotsTxtParser\Handler
 */
final class DatabaseHandler
{
    /**
     * Cache table name
     */
    const TABLE_CACHE = 'robotstxt__cache1';

    /**
     * Delay table name
     */
    const TABLE_DELAY = 'robotstxt__delay0';

    /**
     * MySQL driver name
     */
    const DRIVER_MYSQL = 'mysql';

    /**
     * Configuration data
     */
    private $config = [
        null => [ // Class initialization
            self::DRIVER_MYSQL => [
                'session_get' => 'SELECT @robotstxt;',
                'session_set' => 'SET @robotstxt = 1;',
            ],
        ],
        self::TABLE_CACHE => [
            'readme' => 'https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/sql/cache.md',
            self::DRIVER_MYSQL => [
                'file' => __DIR__ . '/../../res/Cache/MySQL.sql',
                'query' => 'SELECT 1 FROM robotstxt__cache1 LIMIT 1;',
                'session_get' => 'SELECT @robotstxt_cache;',
                'session_set' => 'SET @robotstxt_cache = 1;',
            ],
        ],
        self::TABLE_DELAY => [
            'readme' => 'https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/sql/delay.md',
            self::DRIVER_MYSQL => [
                'file' => __DIR__ . '/../../res/Delay/MySQL.sql',
                'query' => 'SELECT 1 FROM robotstxt__delay0 LIMIT 1;',
                'session_get' => 'SELECT @robotstxt_delay;',
                'session_set' => 'SET @robotstxt_delay = 1;',
            ],
        ]
    ];

    /**
     * Database handler
     * @var PDO
     */
    private $pdo;

    /**
     * Driver
     * @var string
     */
    private $driver;

    /**
     * Delay manager
     * @var Client\Delay\ManagerInterface
     */
    private $delayManager;

    /**
     * DriverHandler constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if (!$this->hasSessionVar(null)) {
            if ($this->pdo->getAttribute(PDO::ATTR_ERRMODE) === PDO::ERRMODE_SILENT) {
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            }
            $this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
            $this->pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
            $this->driverDependents();
            $this->setSessionVar(null);
        }
    }

    /**
     * Get session variable
     *
     * @param string|null $table
     * @return bool
     */
    private function hasSessionVar($table)
    {
        if (!isset($this->config[$table][$this->driver]['session_get'])) {
            return false;
        }
        $query = $this->pdo->query($this->config[$table][$this->driver]['session_get']);
        return (
            $query->rowCount() > 0 &&
            $query->fetchColumn(0) !== null
        );
    }

    /**
     * Driver special treatment
     *
     * @return bool
     */
    private function driverDependents()
    {
        switch ($this->driver) {
            case self::DRIVER_MYSQL:
                $this->pdo->exec('SET NAMES utf8');
                break;
        }
        return true;
    }

    /**
     * Set session variable
     *
     * @param string|null $table
     * @return bool
     */
    private function setSessionVar($table)
    {
        if (!isset($this->config[$table][$this->driver]['session_set'])) {
            return false;
        }
        return $this->pdo->exec($this->config[$table][$this->driver]['session_set']);
    }

    /**
     * Delay client
     *
     * @param string $baseUri
     * @param string $userAgent
     * @param float|int $delay
     * @return Client\Delay\ClientInterface
     * @throws DatabaseException
     */
    public function delayClient($baseUri, $userAgent, $delay)
    {
        $parser = new UserAgentParser($userAgent);
        $userAgent = strtolower($parser->getProduct());
        switch ($this->driver) {
            case self::DRIVER_MYSQL:
                $this->initialCheck(self::TABLE_DELAY);
                return new Client\Delay\MySQL\Client($this->pdo, $baseUri, $userAgent, $delay);
        }
        throw new DatabaseException('Unsupported database. ' . $this->config[self::TABLE_DELAY]['readme']);
    }

    /**
     * Initial table setup check
     *
     * @param string $table
     * @return bool
     * @throws DatabaseException
     */
    private function initialCheck($table)
    {
        if ($this->hasSessionVar($table)) {
            return true;
        }
        try {
            $this->pdo->query($this->config[$table][$this->driver]['query']);
        } catch (\Exception $exception1) {
            try {
                $this->pdo->query(file_get_contents($this->config[$table][$this->driver]['file']));
            } catch (\Exception $exception2) {
                throw new DatabaseException('Missing table `' . $table . '`. Setup instructions: ' . $this->config[$table]['readme']);
            }
        }
        $this->setSessionVar($table);
        return true;
    }

    /**
     * Cache manager
     *
     * @param array $curlOptions
     * @param int|null $byteLimit
     * @return Client\Cache\MySQL\Manager
     * @throws DatabaseException
     */
    public function cacheManager(array $curlOptions, $byteLimit)
    {
        switch ($this->driver) {
            case self::DRIVER_MYSQL:
                $this->initialCheck(self::TABLE_CACHE);
                return new Client\Cache\MySQL\Manager($this->pdo, $curlOptions, $byteLimit);
        }
        throw new DatabaseException('Unsupported database. ' . $this->config[self::TABLE_CACHE]['readme']);
    }

    /**
     * Delay manager
     *
     * @return Client\Delay\ManagerInterface
     * @throws DatabaseException
     */
    public function delayManager()
    {
        if ($this->delayManager !== null) {
            return $this->delayManager;
        }
        switch ($this->driver) {
            case self::DRIVER_MYSQL:
                $this->initialCheck(self::TABLE_DELAY);
                return $this->delayManager = new Client\Delay\MySQL\Manager($this->pdo);
        }
        throw new DatabaseException('Unsupported database. ' . $this->config[self::TABLE_DELAY]['readme']);
    }
}
