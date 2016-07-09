<?php
namespace vipnytt\RobotsTxtParser;

use PDO;
use vipnytt\RobotsTxtParser\Client\Cache\ManagerInterface;
use vipnytt\RobotsTxtParser\Handler\DatabaseHandler;
use vipnytt\RobotsTxtParser\Parser\UriParser;

/**
 * Class Cache
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/CacheManager.md for documentation
 * @package vipnytt\RobotsTxtParser
 */
class Cache implements RobotsTxtInterface
{
    use UriParser;

    /**
     * Client nextUpdate margin in seconds
     * @var int
     */
    protected $clientUpdateBuffer = 300;

    /**
     * Handler
     * @var ManagerInterface
     */
    private $handler;

    /**
     * Cache constructor.
     *
     * @param PDO $pdo
     * @param array $curlOptions
     * @param int|null $byteLimit
     */
    public function __construct(PDO $pdo, array $curlOptions = [], $byteLimit = self::BYTE_LIMIT)
    {
        $handler = new DatabaseHandler($pdo);
        $this->handler = $handler->cacheManager($curlOptions, $byteLimit);
    }

    /**
     * Parser client
     *
     * @param string $baseUri
     * @return TxtClient
     */
    public function client($baseUri)
    {
        return $this->handler->client($this->uriBase($baseUri), $this->clientUpdateBuffer);
    }

    /**
     * Process the update queue
     *
     * @param float|int $targetTime
     * @param int|null $workerID
     * @return string[]
     */
    public function cron($targetTime = 60, $workerID = null)
    {
        return $this->handler->cron($targetTime, $workerID);
    }

    /**
     * Clean the cache table
     *
     * @param int $delay - in seconds
     * @return bool
     */
    public function clean($delay = 900)
    {
        return $this->handler->clean($delay);
    }

    /**
     * Invalidate cache
     *
     * @param $baseUri
     * @return bool
     */
    public function invalidate($baseUri)
    {
        return $this->handler->invalidate($this->uriBase($baseUri));
    }
}
