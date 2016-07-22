<?php
namespace vipnytt\RobotsTxtParser\Client\Delay\MySQL;

use PDO;
use vipnytt\RobotsTxtParser\Client\Delay\ManagerInterface;
use vipnytt\RobotsTxtParser\Exceptions\DatabaseException;

/**
 * Class Manager
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/DelayInterface.md for documentation
 * @package vipnytt\RobotsTxtParser\Handler\Delay\MySQL
 */
class Manager implements ManagerInterface
{
    /**
     * Database handler
     * @var PDO
     */
    private $pdo;

    /**
     * Manager constructor.
     *
     * @param PDO $pdo
     * @throws DatabaseException
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Clean the delay table
     *
     * @param int $delay - in seconds
     * @return bool
     */
    public function clean($delay)
    {
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__delay0
WHERE delayUntil < ((UNIX_TIMESTAMP() - :delay) * 1000000);
SQL
        );
        $query->bindParam(':delay', $delay, PDO::PARAM_INT);
        return $query->execute();
    }

    /**
     * Top X wait time
     *
     * @param int $limit
     * @param int $min
     * @return array
     */
    public function getTopWaitTimes($limit, $min)
    {
        $query = $this->pdo->prepare(<<<SQL
SELECT
  base,
  userAgent,
  delayUntil / 1000000,
  lastDelay / 1000000
FROM robotstxt__delay0
WHERE delayUntil > ((UNIX_TIMESTAMP(CURTIME(6)) + :minDelay) * 1000000)
ORDER BY delayUntil DESC
LIMIT :maxCount;
SQL
        );
        $query->bindParam(':minDelay', $min, PDO::PARAM_INT);
        $query->bindParam(':maxCount', $limit, PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() > 0) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }
}
