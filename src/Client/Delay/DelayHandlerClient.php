<?php
namespace vipnytt\RobotsTxtParser\Client\Delay;

use PDO;
use vipnytt\RobotsTxtParser\SQL\SQLInterface;
use vipnytt\RobotsTxtParser\SQL\SQLTrait;

/**
 * Class DelayClient
 *
 * @package vipnytt\RobotsTxtParser\Client\Delay
 */
class DelayHandlerClient implements SQLInterface
{
    use SQLTrait;

    /**
     * Database connection
     * @var PDO
     */
    private $pdo;

    /**
     * PDO driver
     * @var string
     */
    private $driver;

    /**
     * Base UriClient
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
     * DelayClient constructor.
     *
     * @param PDO $pdo
     * @param string $baseUri
     * @param string $userAgent
     * @param float|int $delay
     */
    public function __construct(PDO $pdo, $baseUri, $userAgent, $delay)
    {
        $this->pdo = $this->pdoInitialize($pdo);
        $this->driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($this->driver != 'mysql') {
            trigger_error('Unsupported database. Currently only MySQL 5.6+ are officially supported. ' . self::README_SQL_DELAY, E_USER_WARNING);
        }
        $this->base = $baseUri;
        $this->userAgent = $userAgent;
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
SELECT (microTime / 1000000) - UNIX_TIMESTAMP(CURTIME(6)) AS sec
FROM robotstxt__delay0
WHERE base = :base AND userAgent = :userAgent;
SQL
        );
        $query->bindParam(':base', $this->base, PDO::PARAM_STR);
        $query->bindParam(':userAgent', $this->userAgent, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            return max($row['sec'], 0);
        }
        return 0;
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
     */
    public function getTimeSleepUntil()
    {
        if ($this->delay == 0) {
            return 0;
        }
        $query = $this->pdo->prepare(<<<SQL
SELECT microTime
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
            return $row['microTime'] / 1000000;
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
INSERT INTO robotstxt__delay0 (base, userAgent, microTime, lastDelay)
VALUES (:base, :userAgent, (UNIX_TIMESTAMP(CURTIME(6)) + :delay) * 1000000, ROUND(:delay))
ON DUPLICATE KEY UPDATE
  microTime = GREATEST((UNIX_TIMESTAMP(CURTIME(6)) + :delay) * 1000000, microTime + (:delay * 1000000)),
  lastDelay = ROUND(:delay);
SQL
        );
        $query->bindParam(':base', $this->base, PDO::PARAM_STR);
        $query->bindParam(':userAgent', $this->userAgent, PDO::PARAM_STR);
        $query->bindParam(':delay', $this->delay, is_int($this->delay) ? PDO::PARAM_INT : PDO::PARAM_STR);
        return $query->execute();
    }
}
