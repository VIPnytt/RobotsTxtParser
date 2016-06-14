<?php
namespace vipnytt\RobotsTxtParser;

use PDO;
use vipnytt\RobotsTxtParser\Client\Delay\DelayHandlerClient;
use vipnytt\RobotsTxtParser\Client\Directives\DelayInterface;
use vipnytt\RobotsTxtParser\Exceptions\SQLException;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\SQL\SQLInterface;
use vipnytt\RobotsTxtParser\SQL\SQLTrait;

/**
 * Class DelayHandler
 *
 * @package vipnytt\RobotsTxtParser
 */
class DelayHandler implements SQLInterface
{
    use SQLTrait;
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
        $this->pdo = $this->pdoInitialize($pdo);
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
     * Invalidate delay
     *
     * @param string $baseUri
     * @param string $userAgent
     * @return bool
     */
    public function invalidate($baseUri, $userAgent)
    {
        $base = $this->urlBase($this->urlEncode($baseUri));
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__delay0
WHERE base = :base AND userAgent = :useragent;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_INT);
        $query->bindParam(':useragent', $userAgent, PDO::PARAM_INT);
        return $query->execute();
    }

    /**
     * List all with queue
     *
     * @param int $minSec
     * @param int $limit
     * @return array
     */
    public function listQueue($minSec = 0, $limit = 100)
    {
        $query = $this->pdo->prepare(<<<SQL
SELECT
  base,
  userAgent,
  microTime / 1000000,
  lastDelay
FROM robotstxt__delay0
WHERE microTime > ((UNIX_TIMESTAMP(CURTIME(6)) + :minimum) * 1000000)
ORDER BY microTime DESC
LIMIT :rowlimit;
SQL
        );
        $query->bindParam(':minimum', $minSec, PDO::PARAM_INT);
        $query->bindParam(':rowlimit', $limit, PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() > 0) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
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
WHERE microTime < ((UNIX_TIMESTAMP() - :delay) * 1000000);
SQL
        );
        $query->bindParam(':delay', $delay, PDO::PARAM_INT);
        return $query->execute();
    }

    /**
     * Create SQL table
     *
     * @return bool
     * @throws SQLException
     */
    public function setup()
    {
        if (!$this->createTable($this->pdo, self::TABLE_DELAY, file_get_contents(__DIR__ . '/SQL/delay.sql'))) {
            throw new SQLException('Unable to create table! Please read instructions at ' . self::README_SQL_DELAY);
        }
        return true;
    }
}
