<?php
namespace vipnytt\RobotsTxtParser\Client\Delay\MySQL;

use PDO;
use vipnytt\RobotsTxtParser\Client\Delay\ClientInterface;
use vipnytt\RobotsTxtParser\Exceptions\DatabaseException;
use vipnytt\UserAgentParser;

/**
 * Class Client
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/DelayClient.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Delay\MySQL
 */
class Client implements ClientInterface
{
    /**
     * Database handler
     * @var PDO
     */
    private $pdo;

    /**
     * Base uri
     * @var string
     */
    private $base;

    /**
     * User-agent
     * @var string
     */
    private $userAgent;

    /**
     * Delay
     * @var float|int
     */
    private $delay;

    /**
     * Client constructor.
     *
     * @param PDO $pdo
     * @param string $baseUri
     * @param string $userAgent
     * @param float|int $delay
     * @throws DatabaseException
     */
    public function __construct(PDO $pdo, $baseUri, $userAgent, $delay)
    {
        $this->pdo = $pdo;
        $this->base = $baseUri;
        $uaStringParser = new UserAgentParser($userAgent);
        $this->userAgent = $uaStringParser->stripVersion();
        $this->delay = round($delay, 6, PHP_ROUND_HALF_UP);
    }

    /**
     * Queue
     *
     * @return float|int
     */
    public function getQueue()
    {
        if ($this->delay == 0) {
            return 0;
        }
        $query = $this->pdo->prepare(<<<SQL
SELECT GREATEST(0, (delayUntil / 1000000) - UNIX_TIMESTAMP(CURTIME(6))) AS sec
FROM robotstxt__delay0
WHERE base = :base AND userAgent = :userAgent;
SQL
        );
        $query->bindParam(':base', $this->base, PDO::PARAM_STR);
        $query->bindParam(':userAgent', $this->userAgent, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            return $row['sec'];
        }
        return 0;
    }

    /**
     * Reset queue
     *
     * @return bool
     */
    public function reset()
    {
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__delay0
WHERE base = :base AND userAgent = :useragent;
SQL
        );
        $query->bindParam(':base', $this->base, PDO::PARAM_INT);
        $query->bindParam(':useragent', $this->userAgent, PDO::PARAM_INT);
        return $query->execute();
    }

    /**
     * Sleep
     *
     * @return float|int
     */
    public function sleep()
    {
        $start = microtime(true);
        $until = $this->getTimeSleepUntil();
        if (microtime(true) > $until) {
            return 0;
        }
        try {
            time_sleep_until($until);
        } catch (\Exception $warning) {
            // Timestamp already in the past
        }
        return microtime(true) - $start;
    }

    /**
     * Timestamp with milliseconds
     *
     * @return float|int
     * @throws DatabaseException
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
        $query->bindParam(':base', $this->base, PDO::PARAM_STR);
        $query->bindParam(':userAgent', $this->userAgent, PDO::PARAM_STR);
        $query->execute();
        $this->increment();
        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if (abs(time() - $row['UNIX_TIMESTAMP()']) > 10) {
                throw new DatabaseException('`PHP server` and `SQL server` timestamps are out of sync. Please fix!');
            }
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
        $query->bindParam(':base', $this->base, PDO::PARAM_STR);
        $query->bindParam(':userAgent', $this->userAgent, PDO::PARAM_STR);
        $query->bindParam(':delay', $this->delay, is_int($this->delay) ? PDO::PARAM_INT : PDO::PARAM_STR);
        return $query->execute();
    }
}
