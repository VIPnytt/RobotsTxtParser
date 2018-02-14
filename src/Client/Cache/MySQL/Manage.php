<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Cache\MySQL;

use vipnytt\RobotsTxtParser\Client\Cache\ManageCore;
use vipnytt\RobotsTxtParser\Exceptions;

/**
 * Class Manage
 *
 * @see https://vipnytt.github.io/RobotsTxtParser/methods/Cache.html for documentation
 * @package vipnytt\RobotsTxtParser\Handler\Cache\MySQL
 */
class Manage extends ManageCore
{
    /**
     * Process the update queue
     *
     * @param float|int $timeLimit
     * @param int|null $workerID
     * @return string[]
     * @throws Exceptions\DatabaseException
     */
    public function cron($timeLimit = self::CRON_EXEC_TIME, $workerID = self::WORKER_ID)
    {
        $start = microtime(true);
        $workerID = $this->setWorkerID($workerID);
        $log = [];
        $lastCount = -1;
        while (count($log) > $lastCount &&
            (
                empty($timeLimit) ||
                $timeLimit > (microtime(true) - $start)
            )
        ) {
            $lastCount = count($log);
            $query = $this->pdo->prepare(<<<SQL
UPDATE robotstxt__cache1
SET worker = :workerID
WHERE worker IS NULL AND nextUpdate <= UNIX_TIMESTAMP()
ORDER BY nextUpdate ASC
LIMIT 1;
SQL
            );
            $query->bindValue('workerID', $workerID, \PDO::PARAM_INT);
            $query->execute();
            $query = $this->pdo->prepare(<<<SQL
SELECT base
FROM robotstxt__cache1
WHERE worker = :workerID
ORDER BY nextUpdate DESC
LIMIT 10;
SQL
            );
            $query->bindValue('workerID', $workerID, \PDO::PARAM_INT);
            $query->execute();
            while ($baseUri = $query->fetch(\PDO::FETCH_COLUMN)) {
                if ((new Base($this->pdo, $baseUri, $this->curlOptions, $this->byteLimit))->refresh()) {
                    $log[(string)microtime(true)] = $baseUri;
                }
            }
        }
        return $log;
    }

    /**
     * Set WorkerID
     *
     * @param int|null $workerID
     * @return int
     * @throws \InvalidArgumentException
     */
    private function setWorkerID($workerID)
    {
        if ($workerID >= 1 &&
            $workerID <= 255
        ) {
            return (int)$workerID;
        } elseif ($workerID !== null) {
            throw new \InvalidArgumentException('WorkerID out of range (1-255)');
        }
        return rand(1, 255);
    }

    /**
     * Base class
     *
     * @param string $baseUri
     * @return Base
     */
    public function base($baseUri)
    {
        return new Base($this->pdo, $baseUri, $this->curlOptions, $this->byteLimit);
    }

    /**
     * Clean the cache table
     *
     * @param int $delay
     * @return bool
     */
    public function clean($delay = self::CLEAN_DELAY)
    {
        $delay += self::CACHE_TIME;
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__cache1
WHERE nextUpdate < (UNIX_TIMESTAMP() - :delay);
SQL
        );
        $query->bindValue('delay', $delay, \PDO::PARAM_INT);
        return $query->execute();
    }
}
