<?php
namespace vipnytt\RobotsTxtParser;

use PDO;
use vipnytt\RobotsTxtParser\Exceptions\SQLException;
use vipnytt\RobotsTxtParser\Parser\UrlParser;
use vipnytt\RobotsTxtParser\SQL\SQLInterface;
use vipnytt\RobotsTxtParser\SQL\SQLTrait;

/**
 * Class CacheHandler
 *
 * @package vipnytt\RobotsTxtParser
 */
class CacheHandler implements RobotsTxtInterface, SQLInterface
{
    use UrlParser;
    use SQLTrait;

    /**
     * Database connection
     * @var PDO
     */
    protected $pdo;

    /**
     * GuzzleHTTP config
     * @var array
     */
    protected $guzzleConfig = [];

    /**
     * Byte limit
     * @var int|null
     */
    protected $byteLimit = self::BYTE_LIMIT;

    /**
     * Client nextUpdate margin in seconds
     * @var int
     */
    protected $clientUpdateMargin = 300;

    /**
     * PDO driver
     * @var string
     */
    private $driver;

    /**
     * CacheHandler constructor.
     *
     * @param PDO $pdo
     * @param array $guzzleConfig
     * @param int|null $byteLimit
     */
    public function __construct(PDO $pdo, array $guzzleConfig = [], $byteLimit = self::BYTE_LIMIT)
    {
        $this->pdo = $this->pdoInitialize($pdo);
        $this->driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($this->driver != 'mysql') {
            trigger_error('Unsupported database. Currently supports MySQL only. ' . self::README_SQL_CACHE, E_USER_WARNING);
        }
        $this->guzzleConfig = $guzzleConfig;
        $this->byteLimit = $byteLimit;
    }

    /**
     * Parser client
     *
     * @param string $baseUri
     * @return TxtClient
     */
    public function client($baseUri)
    {
        $base = $this->urlBase($this->urlEncode($baseUri));
        $query = $this->pdo->prepare(<<<SQL
SELECT
  content,
  statusCode,
  nextUpdate,
  worker,
  UNIX_TIMESTAMP()
FROM robotstxt__cache0
WHERE base = :base;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if ($row['nextUpdate'] > ($row['UNIX_TIMESTAMP()'] - $this->clientUpdateMargin)) {
                $this->markAsActive($base, $row['worker']);
                return new TxtClient($base, $row['statusCode'], $row['content'], self::ENCODING, $this->byteLimit);
            }
        }
        $request = new UriClient($base, $this->guzzleConfig, $this->byteLimit);
        $this->push($request);
        $this->markAsActive($base);
        return new TxtClient($base, $request->getStatusCode(), $request->getContents(), self::ENCODING, $this->byteLimit);
    }

    /**
     * Mark robots.txt as active
     *
     * @param string $base
     * @param int|null $workerID
     * @return bool
     */
    private function markAsActive($base, $workerID = 0)
    {
        if ($workerID == 0) {
            $query = $this->pdo->prepare(<<<SQL
UPDATE robotstxt__cache0
SET worker = NULL
WHERE base = :base AND worker = 0;
SQL
            );
            $query->bindParam(':base', $base, PDO::PARAM_STR);
            return $query->execute();
        }
        return true;
    }

    /**
     * Update an robots.txt in the database
     *
     * @param UriClient $request
     * @return bool
     */
    public function push(UriClient $request)
    {
        $base = $request->getBaseUri();
        $statusCode = $request->getStatusCode();
        $nextUpdate = $request->nextUpdate();
        if (
            $statusCode >= 500 &&
            $statusCode < 600 &&
            mb_stripos($base, 'http') === 0 &&
            $this->displacePush($base, $nextUpdate)
        ) {
            return true;
        }
        $validUntil = $request->validUntil();
        $content = $request->render();
        $query = $this->pdo->prepare(<<<SQL
INSERT INTO robotstxt__cache0 (base, content, statusCode, validUntil, nextUpdate)
VALUES (:base, :content, :statusCode, :validUntil, :nextUpdate)
ON DUPLICATE KEY UPDATE content = :content, statusCode = :statusCode, validUntil = :validUntil,
  nextUpdate = :nextUpdate, worker = 0;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_STR);
        $query->bindParam(':content', $content, PDO::PARAM_STR);
        $query->bindParam(':statusCode', $statusCode, PDO::PARAM_INT);
        $query->bindParam(':validUntil', $validUntil, PDO::PARAM_INT);
        $query->bindParam(':nextUpdate', $nextUpdate, PDO::PARAM_INT);
        return $query->execute();
    }

    /**
     * Displace push timestamp
     *
     * @param string $base
     * @param int $nextUpdate
     * @return bool
     */
    private function displacePush($base, $nextUpdate)
    {
        $query = $this->pdo->prepare(<<<SQL
SELECT
  validUntil,
  UNIX_TIMESTAMP()
FROM robotstxt__cache0
WHERE base = :base;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if ($row['validUntil'] > $row['UNIX_TIMESTAMP()']) {
                $nextUpdate = min($row['validUntil'], $nextUpdate);
                $query = $this->pdo->prepare(<<<SQL
UPDATE robotstxt__cache0
SET nextUpdate = :nextUpdate, worker = NULL
WHERE base = :base;
SQL
                );
                $query->bindParam(':base', $base, PDO::PARAM_STR);
                $query->bindParam(':nextUpdate', $nextUpdate, PDO::PARAM_INT);
                return $query->execute();
            }
        }
        return false;
    }

    /**
     * Process the update queue
     *
     * @param int|null $workerID
     * @return bool
     */
    public function cron($workerID = null)
    {
        $worker = $this->setWorkerID($workerID);
        $result = true;
        while ($result) {
            $query = $this->pdo->prepare(<<<SQL
UPDATE robotstxt__cache0
SET worker = :workerID
WHERE worker IS NULL AND nextUpdate <= UNIX_TIMESTAMP()
ORDER BY nextUpdate ASC
LIMIT 1;
SELECT base
FROM robotstxt__cache0
WHERE worker = :workerID;
SQL
            );
            $query->bindParam(':workerID', $worker, PDO::PARAM_INT);
            $query->execute();
            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $result = $this->push(new UriClient($row['base'], $this->guzzleConfig, $this->byteLimit));
                }
                continue;
            }
            return true;
        }
        return false;
    }

    /**
     * Set WorkerID
     *
     * @param int|null $workerID
     * @return int
     */
    protected function setWorkerID($workerID = null)
    {
        if (
            is_int($workerID) &&
            $workerID <= 255 &&
            $workerID >= 1
        ) {
            return $workerID;
        } elseif ($workerID !== null) {
            trigger_error('WorkerID out of range (1-255)', E_USER_WARNING);
        }
        return rand(1, 255);
    }

    /**
     * Clean the cache table
     *
     * @param int $delay - in seconds
     * @return bool
     */
    public function clean($delay = 600)
    {
        $delay = self::CACHE_TIME + $delay;
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__cache0
WHERE worker = 0 AND nextUpdate < (UNIX_TIMESTAMP() - :delay);
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
        if (!$this->createTable($this->pdo, self::TABLE_CACHE, file_get_contents(__DIR__ . '/SQL/cache.sql'))) {
            throw new SQLException('Unable to create table! Please read instructions at ' . self::README_SQL_CACHE);
        }
        return true;
    }
}
