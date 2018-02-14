<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Delay\MySQL;

use vipnytt\RobotsTxtParser\Client\Delay\ManageCore;

/**
 * Class Manage
 *
 * @see https://vipnytt.github.io/RobotsTxtParser/methods/DelayInterface.html for documentation
 * @package vipnytt\RobotsTxtParser\Handler\Delay\MySQL
 */
class Manage extends ManageCore
{
    /**
     * Clean the delay table
     *
     * @return bool
     */
    public function clean()
    {
        $delay = self::OUT_OF_SYNC_TIME_LIMIT;
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__delay0
WHERE delayUntil < ((UNIX_TIMESTAMP() - :delay) * 1000000);
SQL
        );
        $query->bindValue('delay', $delay, \PDO::PARAM_INT);
        return $query->execute();
    }

    /**
     * Top X wait time
     *
     * @param int $limit
     * @param int $minDelay
     * @return array
     */
    public function getTopWaitTimes($limit = self::TOP_X_LIMIT, $minDelay = self::TOP_X_MIN_DELAY)
    {
        $query = $this->pdo->prepare(<<<SQL
SELECT
  base,
  userAgent,
  delayUntil / 1000000 AS delayUntil,
  lastDelay / 1000000 AS lastDelay
FROM robotstxt__delay0
WHERE delayUntil > ((UNIX_TIMESTAMP(CURTIME(6)) + :minDelay) * 1000000)
ORDER BY delayUntil DESC
LIMIT :maxCount;
SQL
        );
        $query->bindValue('minDelay', $minDelay, \PDO::PARAM_INT);
        $query->bindValue('maxCount', $limit, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Base class
     *
     * @param string $baseUri
     * @param string $userAgent
     * @param float|int $delay
     * @return Base
     */
    public function base($baseUri, $userAgent, $delay)
    {
        return new Base($this->pdo, $baseUri, $userAgent, $delay);
    }
}
