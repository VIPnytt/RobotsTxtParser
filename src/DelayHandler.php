<?php
namespace vipnytt\RobotsTxtParser;

use PDO;
use vipnytt\RobotsTxtParser\Client\Directives\DelayHandlerClient;
use vipnytt\RobotsTxtParser\Client\Directives\DelayInterface;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\SQL\SQLInterface;

/**
 * Class DelayHandler
 *
 * @package vipnytt\RobotsTxtParser
 */
class DelayHandler implements SQLInterface
{
    use UrlParser;

    /**
     * Database connection
     * @var PDO
     */
    private $pdo;

    /**
     * DelayHandler constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        if ($this->pdo->getAttribute(PDO::ATTR_ERRMODE) === PDO::ERRMODE_SILENT) {
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        }
        $this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        $this->pdo->exec('SET NAMES ' . self::SQL_ENCODING);
    }

    /**
     * Client
     *
     * @param DelayInterface $client
     * @return DelayHandlerClient
     */
    public function client(DelayInterface $client)
    {
        return new DelayHandlerClient($this->pdo, $client->getBaseUri(), $client->getUserAgent(), $client->getValue());
    }

    /**
     * Clean the delay table
     *
     * @param int $delay - in seconds
     * @return bool
     */
    public function clean($delay = 60)
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
     * Top X delays
     *
     * @param int $limit
     * @param int $min
     * @return array
     */
    public function getTopDelays($limit = 100, $min = 0)
    {
        $query = $this->pdo->prepare(<<<SQL
SELECT
  base,
  userAgent,
  delayUntil / 1000000,
  lastDelay / 1000000
FROM robotstxt__delay0
WHERE lastDelay > (:minDelay * 1000000)
ORDER BY lastDelay DESC
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

    /**
     * Top X wait time
     *
     * @param int $limit
     * @param int $min
     * @return array
     */
    public function getTopWaitTime($limit = 100, $min = 0)
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
