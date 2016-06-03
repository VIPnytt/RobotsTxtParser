<?php
namespace vipnytt\RobotsTxtParser;

use PDO;
use vipnytt\RobotsTxtParser\Client\SQL\SQLInterface;
use vipnytt\RobotsTxtParser\Client\SQL\SQLTrait;
use vipnytt\RobotsTxtParser\Parser\UrlParser;

/**
 * Class SQL
 *
 * @package vipnytt\RobotsTxtParser
 */
class SQL implements RobotsTxtInterface, SQLInterface
{
    use SQLTrait;
    use UrlParser;

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
     * GuzzleHTTP config
     * @var array
     */
    private $guzzleConfig = [];

    /**
     * Byte limit
     * @var int
     */
    private $byteLimit = self::BYTE_LIMIT;

    /**
     * Client nextUpdate margin in seconds
     * @var int
     */
    private $clientUpdateMargin = 300;

    /**
     * Cache constructor.
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
WHERE worker = :worker;
SQL
            );
            $query->bindParam(':workerID', $worker, PDO::PARAM_INT);
            $query->execute();
            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $result = $this->push(new URI($row['base'], $this->guzzleConfig, $this->byteLimit));
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
    private function setWorkerID($workerID = null)
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
     * Update an robots.txt in the database
     *
     * @param URI $request
     * @return bool
     */
    public function push(URI $request)
    {
        $base = $request->getBaseUri();
        $statusCode = $request->getStatusCode();
        $nextUpdate = $request->nextUpdate();
        if (
            $statusCode >= 500 &&
            $statusCode < 600 &&
            mb_strpos($base, 'http') === 0
        ) {
            $query = $this->pdo->prepare(<<<SQL
SELECT validUntil
FROM robotstxt__cache0
WHERE base = :base;
SQL
            );
            $query->bindParam(':base', $base, PDO::PARAM_STR);
            $query->execute();
            if (
                $query->rowCount() > 0 &&
                ($existingValidUntil = $query->fetch(PDO::FETCH_ASSOC)['validUntil']) > time()
            ) {
                $nextUpdate = min($existingValidUntil, $nextUpdate);
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
        $validUntil = $request->validUntil();
        $content = $request->getContents();
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
     * Parser client
     *
     * @param string $baseUri
     * @return Core
     */
    public function client($baseUri)
    {
        $base = $this->urlBase($this->urlEncode($baseUri));
        $query = $this->pdo->prepare(<<<SQL
SELECT
  content,
  statusCode,
  nextUpdate,
  worker
FROM robotstxt__cache0
WHERE base = :base;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if ($row['nextUpdate'] >= (time() - $this->clientUpdateMargin)) {
                $this->markAsActive($base, $row['worker']);
                return new Core($base, $row['code'], $row['content'], self::ENCODING, $this->byteLimit);
            }
        }
        $request = new URI($base, $this->guzzleConfig, $this->byteLimit);
        $this->push($request);
        $this->markAsActive($base);
        return new Core($base, $request->getStatusCode(), $request->getContents(), self::ENCODING, $this->byteLimit);
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
}
