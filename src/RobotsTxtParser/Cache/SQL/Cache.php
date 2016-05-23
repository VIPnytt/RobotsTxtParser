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
        $workerID = $this->setWorkerID($workerID);
        $result = true;
        while ($result) {
            $query = $this->pdo->prepare(<<<SQL
UPDATE robotstxt__cache0
SET workerID = :workerID
WHERE workerID IS NULL AND nextUpdate <= NOW()
ORDER BY nextUpdate ASC
LIMIT 1;
SELECT
  base,
  validUntil
FROM robotstxt__cache0
WHERE workerID = :workerID;
SQL
            );
            $query->bindParam(':workerID', $workerID, PDO::PARAM_INT);
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
SET nextUpdate = :nextUpdate, workerID = NULL
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
ON DUPLICATE KEY UPDATE content = :content, statusCode = :statusCode, validUntil = :validUntil, nextUpdate = :nextUpdate, workerID = 0;
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
SELECT content,statusCode,nextUpdate,workerID
FROM robotstxt__cache0
WHERE base = :base;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if ($row['nextUpdate'] >= (time() - $this->clientNextUpdateMargin)) {
                $this->markAsActive($base, $row['workerID']);
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
SET workerID = NULL
WHERE base = :base;
SQL
            );
            $query->bindParam(':base', $base, PDO::PARAM_STR);
            return $query->execute();
        }
        return true;
    }

    /**
     * Database maintenance
     *
     * @return bool
     */
    public function cleanup()
    {
        $nextUpdate = time() - self::CACHE_TIME;
        $microTime = microtime(true) * 1000000;
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__cache0
WHERE workerID = 0 AND nextUpdate < :nextUpdate;
DELETE FROM robotstxt__delay0
WHERE microTime < :microTime;
SQL
        );
        $query->bindParam(':nextUpdate', $nextUpdate, PDO::PARAM_INT);
        $query->bindParam(':microTime', $microTime, PDO::PARAM_INT);
        return $query->execute();
    }

    /**
     * Honor the Crawl-delay rules
     *
     * @param int|float $delay
     * @param string $baseUri
     * @param string $userAgent
     * @return true
     */
    public function delaySleep($delay, $baseUri, $userAgent = self::USER_AGENT)
    {
        $until = $this->delayUntil($delay, $baseUri, $userAgent);
        if (microtime(true) > $until) {
            return true;
        }
        try {
            time_sleep_until($until);
        } catch (\Exception $warning) {
            // Timestamp already in the past
        }
        return true;
    }

    /**
     * @param int|float $delay
     * @param string $baseUri
     * @param string $userAgent
     * @return int|float|false
     */
    public function delayUntil($delay, $baseUri, $userAgent = self::USER_AGENT)
    {
        if ($delay <= 0) {
            return false;
        }
        $base = $this->urlBase($this->urlEncode($baseUri));
        $query = $this->pdo->prepare(<<<SQL
SELECT microTime
FROM robotstxt__delay0
WHERE base = :base AND userAgent = :userAgent;
SQL
        );
        $query->bindParam(':base', $base, PDO::PARAM_STR);
        $query->bindParam(':userAgent', $userAgent, PDO::PARAM_STR);
        $query->execute();
        $this->setDelay($delay, $base, $userAgent);
        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            return $row['microTime'] / 1000000;
        }
        return 0;
    }

    /**
     * Set new delayUntil timestamp
     *
     * @param int|float $delay
     * @param string $baseUri
     * @param string $userAgent
     * @return bool
     */
    protected function setDelay($delay, $baseUri, $userAgent = self::USER_AGENT)
    {
        $delay = $delay * 1000000;
        $microTime = (microtime(true) * 1000000) + $delay;
        $query = $this->pdo->prepare(<<<SQL
INSERT INTO robotstxt__delay0 (base, userAgent, microTime)
VALUES (:base, :userAgent, :microTime)
ON DUPLICATE KEY UPDATE microTime = GREATEST(:microTime, microTime + :delay);
SQL
        );
        $query->bindParam(':base', $baseUri, PDO::PARAM_STR);
        $query->bindParam(':userAgent', $userAgent, PDO::PARAM_STR);
        $query->bindParam(':microTime', $microTime, PDO::PARAM_INT);
        $query->bindParam(':delay', $delay, PDO::PARAM_INT);
        return $query->execute();
    }
}
