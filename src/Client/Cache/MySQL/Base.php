<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Cache\MySQL;

use vipnytt\RobotsTxtParser\Client\Cache\BaseCore;
use vipnytt\RobotsTxtParser\Exceptions;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;
use vipnytt\RobotsTxtParser\TxtClient;
use vipnytt\RobotsTxtParser\UriClient;

/**
 * Class Base
 *
 * @see https://vipnytt.github.io/RobotsTxtParser/methods/Cache.html for documentation
 * @package vipnytt\RobotsTxtParser\Handler\Cache\MySQL
 */
class Base extends BaseCore implements RobotsTxtInterface
{
    /**
     * Debug - Get raw data
     *
     * @return array
     */
    public function debug()
    {
        $query = $this->pdo->prepare(<<<SQL
SELECT *
FROM robotstxt__cache1
WHERE base = :base;
SQL
        );
        $query->bindValue('base', $this->base, \PDO::PARAM_STR);
        $query->execute();
        return $query->rowCount() > 0 ? $query->fetch(\PDO::FETCH_ASSOC) : [];
    }

    /**
     * Parser client
     *
     * @return TxtClient
     * @throws Exceptions\OutOfSyncException
     * @throws Exceptions\DatabaseException
     */
    public function client()
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
        $query->bindValue('base', $this->base, \PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            $row = $query->fetch(\PDO::FETCH_ASSOC);
            $this->clockSyncCheck($row['UNIX_TIMESTAMP()'], self::OUT_OF_SYNC_TIME_LIMIT);
            if ($row['nextUpdate'] >= $row['UNIX_TIMESTAMP()']) {
                $this->markAsActive($row['worker']);
                return new TxtClient($this->base, $row['statusCode'], $row['content'], self::ENCODING, $row['effective'], $this->byteLimit);
            }
        }
        $query = $this->pdo->prepare(<<<SQL
UPDATE robotstxt__cache1
SET worker = 0
WHERE base = :base;
SQL
        );
        $query->bindValue('base', $this->base, \PDO::PARAM_STR);
        $query->execute();
        return $this->refresh();
    }

    /**
     * Mark robots.txt as active
     *
     * @param int|null $workerID
     * @return bool
     */
    private function markAsActive($workerID = 0)
    {
        if ($workerID == 0) {
            $query = $this->pdo->prepare(<<<SQL
UPDATE robotstxt__cache1
SET worker = NULL
WHERE base = :base AND worker = 0;
SQL
            );
            $query->bindValue('base', $this->base, \PDO::PARAM_STR);
            return $query->execute();
        }
        return true;
    }

    /**
     * Update the robots.txt
     *
     * @return UriClient
     * @throws Exceptions\DatabaseException
     */
    public function refresh()
    {
        $client = new UriClient($this->base, $this->curlOptions, $this->byteLimit);
        $effective = $client->getEffectiveUri();
        if ($effective == $this->base) {
            $effective = null;
        }
        $statusCode = $client->getStatusCode();
        $nextUpdate = $client->nextUpdate();
        if (strpos($this->base, 'http') === 0 &&
            (
                $statusCode === null ||
                (
                    $statusCode >= 500 &&
                    $statusCode < 600
                )
            ) &&
            $this->displacePush($nextUpdate)
        ) {
            return $client;
        }
        $validUntil = $client->validUntil();
        $content = $client->render()->compressed(self::RENDER_LINE_SEPARATOR);
        $query = $this->pdo->prepare(<<<SQL
INSERT INTO robotstxt__cache1 (base, content, statusCode, validUntil, nextUpdate, effective)
VALUES (:base, :content, :statusCode, :validUntil, :nextUpdate, :effective)
ON DUPLICATE KEY UPDATE content = :content, statusCode = :statusCode, validUntil = :validUntil,
  nextUpdate = :nextUpdate, effective = :effective, worker = 0;
SQL
        );
        $query->bindValue('base', $this->base, \PDO::PARAM_STR);
        $query->bindValue('content', $content, \PDO::PARAM_STR);
        $query->bindValue('statusCode', $statusCode, \PDO::PARAM_INT | \PDO::PARAM_NULL);
        $query->bindValue('validUntil', $validUntil, \PDO::PARAM_INT);
        $query->bindValue('nextUpdate', $nextUpdate, \PDO::PARAM_INT);
        $query->bindValue('effective', $effective, \PDO::PARAM_STR | \PDO::PARAM_NULL);
        $query->execute();
        return $client;
    }

    /**
     * Displace push timestamp
     *
     * @param int $nextUpdate
     * @return bool
     * @throws Exceptions\OutOfSyncException
     */
    private function displacePush($nextUpdate)
    {
        $query = $this->pdo->prepare(<<<SQL
SELECT
  validUntil,
  UNIX_TIMESTAMP()
FROM robotstxt__cache1
WHERE base = :base;
SQL
        );
        $query->bindValue('base', $this->base, \PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            $row = $query->fetch(\PDO::FETCH_ASSOC);
            $this->clockSyncCheck($row['UNIX_TIMESTAMP()'], self::OUT_OF_SYNC_TIME_LIMIT);
            if ($row['validUntil'] > $row['UNIX_TIMESTAMP()']) {
                $nextUpdate = min($row['validUntil'], $nextUpdate);
                $query = $this->pdo->prepare(<<<SQL
UPDATE robotstxt__cache1
SET nextUpdate = :nextUpdate, worker = 0
WHERE base = :base;
SQL
                );
                $query->bindValue('base', $this->base, \PDO::PARAM_STR);
                $query->bindValue('nextUpdate', $nextUpdate, \PDO::PARAM_INT);
                return $query->execute();
            }
        }
        return false;
    }

    /**
     * Invalidate cache
     *
     * @return bool
     */
    public function invalidate()
    {
        $query = $this->pdo->prepare(<<<SQL
DELETE FROM robotstxt__cache1
WHERE base = :base;
SQL
        );
        $query->bindValue('base', $this->base, \PDO::PARAM_STR);
        return $query->execute();
    }
}
