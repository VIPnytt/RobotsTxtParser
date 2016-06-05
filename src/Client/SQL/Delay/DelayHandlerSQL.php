<?php
namespace vipnytt\RobotsTxtParser\Client\SQL\Delay;

use PDO;
use vipnytt\RobotsTxtParser\Client\SQL\SQLInterface;
use vipnytt\RobotsTxtParser\Client\SQL\SQLTrait;
use vipnytt\RobotsTxtParser\Parser\UrlParser;

/**
 * Class DelayHandlerSQL
 *
 * @package vipnytt\RobotsTxtParser\Delay\SQL
 */
class DelayHandlerSQL implements SQLInterface
{
    use SQLTrait;
    use UrlParser;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * PDO driver
     * @var string
     */
    private $driver;

    /**
     * Base URI
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
     * Delay constructor.
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
        $this->base = $this->urlBase($this->urlEncode($baseUri));
        $this->userAgent = $userAgent;
        $this->delay = $delay;
    }

    /**
     * Sleep
     *
     * @return float|int
     */
    public function sleep()
    {
        $start = microtime(true);
        $until = $this->getMicroTime();
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
     * @return float|int|false
     */
    public function getMicroTime()
    {
        $this->increment();
        $query = $this->pdo->prepare(<<<SQL
SELECT microTime
FROM robotstxt__delay0
WHERE base = :base AND userAgent = :userAgent;
SQL
        );
        $query->bindParam(':base', $this->base, PDO::PARAM_STR);
        $query->bindParam(':userAgent', $this->userAgent, PDO::PARAM_STR);
        $query->execute();

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
INSERT INTO robotstxt__delay0 (base, userAgent, microTime)
VALUES (:base, :userAgent, microTime + :delay)
ON DUPLICATE KEY UPDATE
  microTime = GREATEST((UNIX_TIMESTAMP(CURTIME(6)) + :delay) * 1000000, microTime + (:delay * 1000000));
SQL
        );
        $query->bindParam(':base', $this->base, PDO::PARAM_STR);
        $query->bindParam(':userAgent', $this->userAgent, PDO::PARAM_STR);
        $query->bindParam(':delay', $this->delay, PDO::PARAM_INT);
        return $query->execute();
    }
}
