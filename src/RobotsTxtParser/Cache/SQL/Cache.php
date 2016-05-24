<?php
namespace vipnytt\RobotsTxtParser\Cache\SQL;

use PDO;
use vipnytt\RobotsTxtParser\Client;
use vipnytt\RobotsTxtParser\Core\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\Core\UrlParser;
use vipnytt\RobotsTxtParser\Request;

/**
 * Class Cache
 *
 * @package vipnytt\RobotsTxtParser\Cache
 */
class Cache implements RobotsTxtInterface
{
    use UrlParser;

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
     * @var int
     */
    protected $byteLimit = self::BYTE_LIMIT;

    /**
     * Client nextUpdate margin in seconds
     * @var int
     */
    protected $clientNextUpdateMargin = 300;

    /**
     * Cache constructor.
     *
     * @param PDO $pdo
     * @param array $guzzleConfig
     * @param int $byteLimit
     */
    public function __construct(PDO $pdo, array $guzzleConfig = [], $byteLimit = self::BYTE_LIMIT)
    {
        $this->pdo = $pdo;
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
SELECT
  base,
  validUntil
FROM robotstxt__cache0
WHERE worker = :worker;
SQL
            );
            $query->bindParam(':workerID', $worker, PDO::PARAM_INT);
            $query->execute();
            if ($query->rowCount() > 0) {
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $result = $this->push(new Request($row['base'], $this->guzzleConfig, $this->byteLimit), $row['validUntil']);
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
     * @param Request $request
     * @param int $existingValidUntil
     * @return bool
     */
    public function push(Request $request, $existingValidUntil = 0)
    {
        $time = time();
        $base = $request->getBaseUri();
        $statusCode = $request->getStatusCode();
        $nextUpdate = $request->nextUpdate();
        if (
            $existingValidUntil > $time &&
            $statusCode >= 500 &&
            $statusCode < 600 &&
            mb_strpos(parse_url($base, PHP_URL_SCHEME), 'http') === 0
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
        $validUntil = $request->validUntil();
        $content = $request->getContents();
        $query = $this->pdo->prepare(<<<SQL
INSERT INTO robotstxt__cache0 (base, content, statusCode, validUntil, nextUpdate)
VALUES (:base, :content, :statusCode, :validUntil, :nextUpdate)
ON DUPLICATE KEY UPDATE content = :content, statusCode = :statusCode, validUntil = :validUntil, nextUpdate = :nextUpdate, worker = 0;
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
     * @return Client|Request
     */
    public function client($baseUri)
    {
        $base = $this->urlBase($this->urlEncode($baseUri));
        $query = $this->pdo->prepare(<<<SQL
SELECT content,statusCode,nextUpdate,worker
FROM robotstxt__cache0
WHERE base = :base;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if ($row['nextUpdate'] >= (time() - $this->clientNextUpdateMargin)) {
                $this->markAsActive($base, $row['worker']);
                return new Client($base, $row['code'], $row['content'], self::ENCODING, $this->byteLimit);
            }
        }
        $request = new Request($base, $this->guzzleConfig, $this->byteLimit);
        $this->push($request);
        $this->markAsActive($base);
        return $request;
    }

    /**
     * Mark robots.txt as active
     *
     * @param string $base
     * @param int|null $workerID
     * @return bool
     */
    protected function markAsActive($base, $workerID = 0)
    {
        if ($workerID == 0) {
            $query = $this->pdo->prepare(<<<SQL
UPDATE robotstxt__cache0
SET worker = NULL
WHERE base = :base;
SQL
            );
            $query->bindParam(':base', $base, PDO::PARAM_STR);
            return $query->execute();
        }
        return true;
    }

    /**
     * Delay
     *
     * @param float|int $delay
     * @param string $baseUri
     * @param string $userAgent
     * @return Delay
     */
    public function delay($delay, $baseUri, $userAgent)
    {
        return new Delay($this->pdo, $delay, $baseUri, $userAgent);
    }
}
