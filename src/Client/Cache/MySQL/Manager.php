<?php
namespace vipnytt\RobotsTxtParser\Client\Cache\MySQL;

use PDO;
use vipnytt\RobotsTxtParser\Client\Cache\ManagerInterface;
use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\RobotsTxtParser\Exceptions\DatabaseException;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\TxtClient;
use vipnytt\RobotsTxtParser\UriClient;

/**
 * Class Manager
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/CacheManager.md for documentation
 * @package vipnytt\RobotsTxtParser\Handler\Cache\MySQL
 */
class Manager implements ManagerInterface, RobotsTxtInterface
{
    /**
     * Database handler
     * @var PDO
     */
    private $pdo;

    /**
     * cURL options
     * @var array
     */
    private $curlOptions = [];

    /**
     * Byte limit
     * @var int|null
     */
    private $byteLimit = self::BYTE_LIMIT;

    /**
     * Manager constructor.
     *
     * @param PDO $pdo
     * @param array $curlOptions
     * @param int|null $byteLimit
     */
    public function __construct(PDO $pdo, array $curlOptions, $byteLimit)
    {
        $this->pdo = $pdo;
        $this->curlOptions = $curlOptions;
        $this->byteLimit = $byteLimit;
    }

    /**
     * Parser client
     *
     * @param string $base
     * @return TxtClient
     */
    public function client($base)
    {
        $query = $this->pdo->prepare(<<<SQL
SELECT
  content,
  statusCode,
  nextUpdate,
  effective,
  worker,
  UNIX_TIMESTAMP()
FROM robotstxt__cache1
WHERE base = :base;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $this->clockSyncCheck($row['UNIX_TIMESTAMP()']);
            if ($row['nextUpdate'] >= $row['UNIX_TIMESTAMP()']) {
                $this->markAsActive($base, $row['worker']);
                return new TxtClient($base, $row['statusCode'], $row['content'], self::ENCODING, $row['effective'], $this->byteLimit);
            }
        }
        $query = $this->pdo->prepare(<<<SQL
UPDATE robotstxt__cache1
SET worker = 0
WHERE base = :base;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_STR);
        $query->execute();
        $request = new UriClient($base, $this->curlOptions, $this->byteLimit);
        $this->push($request, null);
        return $request;
    }

    /**
     * Clock sync check
     *
     * @param int $time
     * @throws DatabaseException
     */
    private function clockSyncCheck($time)
    {
        if (abs(time() - $time) >= 10) {
            throw new DatabaseException('`PHP server` and `SQL server` timestamps are out of sync. Please fix!');
        }
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
UPDATE robotstxt__cache1
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
     * @param UriClient $client
     * @param int|null $worker
     * @return bool
     */
    private function push(UriClient $client, $worker = 0)
    {
        $base = $client->getBaseUri();
        $statusCode = $client->getStatusCode();
        $nextUpdate = $client->nextUpdate();
        $effective = ($effective = $client->getEffectiveUri()) === $base ? null : $effective;
        if (
            stripos($base, 'http') === 0 &&
            (
                $statusCode === null ||
                (
                    $statusCode >= 500 &&
                    $statusCode < 600
                )
            ) &&
            $this->displacePush($base, $nextUpdate)
        ) {
            return true;
        }
        $validUntil = $client->validUntil();
        $content = $client->render();
        $query = $this->pdo->prepare(<<<SQL
INSERT INTO robotstxt__cache1 (base, content, statusCode, validUntil, nextUpdate, effective)
VALUES (:base, :content, :statusCode, :validUntil, :nextUpdate, :effective)
ON DUPLICATE KEY UPDATE content = :content, statusCode = :statusCode, validUntil = :validUntil,
  nextUpdate = :nextUpdate, effective = :effective, worker = :worker;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_STR);
        $query->bindParam(':content', $content, PDO::PARAM_STR);
        $query->bindParam(':statusCode', $statusCode, PDO::PARAM_INT | PDO::PARAM_NULL);
        $query->bindParam(':validUntil', $validUntil, PDO::PARAM_INT);
        $query->bindParam(':nextUpdate', $nextUpdate, PDO::PARAM_INT);
        $query->bindParam(':effective', $effective, PDO::PARAM_STR | PDO::PARAM_NULL);
        $query->bindParam(':worker', $worker, PDO::PARAM_INT | PDO::PARAM_NULL);
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
FROM robotstxt__cache1
WHERE base = :base;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $this->clockSyncCheck($row['UNIX_TIMESTAMP()']);
            if ($row['validUntil'] > $row['UNIX_TIMESTAMP()']) {
                $nextUpdate = min($row['validUntil'], $nextUpdate);
                $query = $this->pdo->prepare(<<<SQL
UPDATE robotstxt__cache1
SET nextUpdate = :nextUpdate, worker = NULL
WHERE base = :base;
SQL
                );
                $query->bindParam(':base', $base, PDO::PARAM_STR);
                $query->bindParam(':nextUpdate', $nextUpdate, PDO::PARAM_INT);
                return $query->execute();
            }
            $this->invalidate($base);
        }
        return false;
    }

    /**
     * Invalidate cache
     *
     * @param $base
     * @return bool
     */
    public function invalidate($base)
    {
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__cache1
WHERE base = :base;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_STR);
        return $query->execute();
    }

    /**
     * Process the update queue
     *
     * @param float|int $targetTime
     * @param int|null $workerID
     * @return string[]
     * @throws ClientException
     */
    public function cron($targetTime, $workerID)
    {
        $start = microtime(true);
        $worker = $this->setWorkerID($workerID);
        $log = [];
        $lastCount = -1;
        while (
            $targetTime > microtime(true) - $start &&
            count($log) > $lastCount
        ) {
            $lastCount = count($log);
            $query = $this->pdo->prepare(<<<SQL
UPDATE robotstxt__cache1
SET worker = :workerID
WHERE worker IS NULL AND nextUpdate <= UNIX_TIMESTAMP()
ORDER BY nextUpdate ASC
LIMIT 1;
SQL
            );
            $query->bindParam(':workerID', $worker, PDO::PARAM_INT);
            $query->execute();
            $query = $this->pdo->prepare(<<<SQL
SELECT base
FROM robotstxt__cache1
WHERE worker = :workerID
LIMIT 10;
SQL
            );
            $query->bindParam(':workerID', $worker, PDO::PARAM_INT);
            $query->execute();
            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    if (!$this->push(new UriClient($row['base'], $this->curlOptions, $this->byteLimit))) {
                        throw new ClientException('Unable to update `' . $row['base'] . '`');
                    }
                    $log[] = $row['base'];
                }
            }
        }
        return $log;
    }

    /**
     * Set WorkerID
     *
     * @param int|null $workerID
     * @return int
     * @throws DatabaseException
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
            throw new DatabaseException('WorkerID out of range (1-255)');
        }
        return rand(1, 255);
    }

    /**
     * Clean the cache table
     *
     * @param int $delay - in seconds
     * @return bool
     */
    public function clean($delay)
    {
        $delay = self::CACHE_TIME + $delay;
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__cache1
WHERE (worker = 0 OR worker IS NULL) AND nextUpdate < (UNIX_TIMESTAMP() - :delay);
SQL
        );
        $query->bindParam(':delay', $delay, PDO::PARAM_INT);
        return $query->execute();
    }
}
