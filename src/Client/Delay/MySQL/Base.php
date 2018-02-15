<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Delay\MySQL;

use vipnytt\RobotsTxtParser\Client\Delay\BaseCore;
use vipnytt\RobotsTxtParser\Exceptions;

/**
 * Class Base
 *
 * @see https://vipnytt.github.io/RobotsTxtParser/methods/DelayClient.html for documentation
 * @package vipnytt\RobotsTxtParser\Client\Delay\MySQL
 */
class Base extends BaseCore
{
    /**
     * Queue
     *
     * @return float|int
     */
    public function checkQueue()
    {
        if ($this->delay == 0) {
            return 0;
        }
        $query = $this->pdo->prepare(<<<SQL
SELECT GREATEST(0, (delayUntil / 1000000) - UNIX_TIMESTAMP(CURTIME(6)))
FROM robotstxt__delay0
WHERE base = :base AND userAgent = :userAgent;
SQL
        );
        $query->bindValue('base', $this->base, \PDO::PARAM_STR);
        $query->bindValue('userAgent', $this->userAgent, \PDO::PARAM_STR);
        $query->execute();
        return $query->rowCount() > 0 ? floatval($query->fetch(\PDO::FETCH_COLUMN)) : 0;
    }

    /**
     * Reset queue
     *
     * @param float|int $newDelay
     * @return bool
     */
    public function reset($newDelay = self::RESET_NEW_DELAY)
    {
        if (empty($newDelay)) {
            $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__delay0
WHERE base = :base AND userAgent = :userAgent;
SQL
            );
            $query->bindValue('base', $this->base, \PDO::PARAM_STR);
            $query->bindValue('userAgent', $this->userAgent, \PDO::PARAM_STR);
            return $query->execute();
        }
        $query = $this->pdo->prepare(<<<SQL
INSERT INTO robotstxt__delay0 (base, userAgent, delayUntil, lastDelay)
VALUES (:base, :userAgent, (UNIX_TIMESTAMP(CURTIME(6)) + :delay) * 1000000, :delay * 1000000)
ON DUPLICATE KEY UPDATE
  delayUntil = (UNIX_TIMESTAMP(CURTIME(6)) + :delay) * 1000000,
  lastDelay  = :delay * 1000000;
SQL
        );
        $query->bindValue('base', $this->base, \PDO::PARAM_STR);
        $query->bindValue('userAgent', $this->userAgent, \PDO::PARAM_STR);
        $query->bindValue('delay', $newDelay, \PDO::PARAM_INT);
        return $query->execute();
    }

    /**
     * Timestamp with milliseconds
     *
     * @return float|int
     * @throws Exceptions\OutOfSyncException
     */
    public function getTimeSleepUntil()
    {
        if ($this->delay == 0) {
            return 0;
        }
        $query = $this->pdo->prepare(<<<SQL
SELECT
  delayUntil,
  UNIX_TIMESTAMP()
FROM robotstxt__delay0
WHERE base = :base AND userAgent = :userAgent;
SQL
        );
        $query->bindValue('base', $this->base, \PDO::PARAM_STR);
        $query->bindValue('userAgent', $this->userAgent, \PDO::PARAM_STR);
        $query->execute();
        $this->increment();
        if ($query->rowCount() > 0) {
            $row = $query->fetch(\PDO::FETCH_ASSOC);
            $this->clockSyncCheck($row['UNIX_TIMESTAMP()'], self::OUT_OF_SYNC_TIME_LIMIT);
            return $row['delayUntil'] / 1000000;
        }
        return 0;
    }

    /**
     * Set new delayUntil timestamp
     *
     * @return bool
     */
    private function increment()
    {
        $query = $this->pdo->prepare(<<<SQL
INSERT INTO robotstxt__delay0 (base, userAgent, delayUntil, lastDelay)
VALUES (:base, :userAgent, (UNIX_TIMESTAMP(CURTIME(6)) + :delay) * 1000000, :delay * 1000000)
ON DUPLICATE KEY UPDATE
  delayUntil = GREATEST((UNIX_TIMESTAMP(CURTIME(6)) + :delay) * 1000000, delayUntil + (:delay * 1000000)),
  lastDelay = :delay * 1000000;
SQL
        );
        $query->bindValue('base', $this->base, \PDO::PARAM_STR);
        $query->bindValue('userAgent', $this->userAgent, \PDO::PARAM_STR);
        $query->bindValue('delay', $this->delay, \PDO::PARAM_INT);
        return $query->execute();
    }

    /**
     * Debug - Get raw data
     *
     * @return array
     */
    public function debug()
    {
        $query = $this->pdo->prepare(<<<SQL
SELECT *
FROM robotstxt__delay0
WHERE base = :base AND userAgent = :userAgent;
SQL
        );
        $query->bindValue('base', $this->base, \PDO::PARAM_STR);
        $query->bindValue('userAgent', $this->userAgent, \PDO::PARAM_STR);
        $query->execute();
        return $query->rowCount() > 0 ? $query->fetch(\PDO::FETCH_ASSOC) : [];
    }
}
