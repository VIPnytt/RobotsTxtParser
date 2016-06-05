<?php
namespace vipnytt\RobotsTxtParser\Client\SQL\Cache;

use PDO;
use vipnytt\RobotsTxtParser\Client\SQL\SQLInterface;
use vipnytt\RobotsTxtParser\Client\SQL\SQLTrait;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\URI;

/**
 * Class CacheCoreSQL
 *
 * @package vipnytt\RobotsTxtParser\Client\SQL\Cache
 */
abstract class CacheCoreSQL implements RobotsTxtInterface, SQLInterface
{
    use SQLTrait;

    /**
     * Database connection
     * @var PDO
     */
    protected $pdo;

    /**
     * PDO driver
     * @var string
     */
    protected $driver;

    /**
     * GuzzleHTTP config
     * @var array
     */
    protected $guzzleConfig = [];

    /**
     * Byte limit
     * @var int
     */
    protected $byteLimit = self::BYTE_LIMIT;

    /**
     * CacheCoreSQL constructor.
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
}
