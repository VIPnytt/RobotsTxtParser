<?php
namespace vipnytt\RobotsTxtParser;

use PDO;
use vipnytt\RobotsTxtParser\Client\SQL\Cache\CacheCoreSQL;
use vipnytt\RobotsTxtParser\Client\SQL\SQLMaintenance;
use vipnytt\RobotsTxtParser\Parser\UrlParser;

/**
 * Class SQL
 *
 * @package vipnytt\RobotsTxtParser
 */
class SQL extends CacheCoreSQL
{
    use UrlParser;

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
        parent::__construct($pdo, $guzzleConfig, $byteLimit);
    }

    /**
     * Parser client
     *
     * @param string $baseUri
     * @return Basic
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
                return new Basic($base, $row['code'], $row['content'], self::ENCODING, $this->byteLimit);
            }
        }
        $request = new URI($base, $this->guzzleConfig, $this->byteLimit);
        $this->push($request);
        $this->markAsActive($base);
        return new Basic($base, $request->getStatusCode(), $request->getContents(), self::ENCODING, $this->byteLimit);
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
     * Maintenance
     *
     * @return SQLMaintenance
     */
    public function maintenance()
    {
        return new SQLMaintenance($this->pdo);
    }
}
