<?php
namespace vipnytt\RobotsTxtParser;

use PDO;
use vipnytt\RobotsTxtParser\Client\Cache\ManagerInterface;
use vipnytt\RobotsTxtParser\Exceptions\ClientException;
use vipnytt\RobotsTxtParser\Handler\DatabaseHandler;
use vipnytt\RobotsTxtParser\Parser\UriParser;

/**
 * Class Cache
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/Cache.md for documentation
 * @package vipnytt\RobotsTxtParser
 */
class Cache implements RobotsTxtInterface
{
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
     * @throws ClientException
     */
    public function client($baseUri)
    {
        $parser = new UriParser($baseUri);
        return $this->handler->client($parser->base());
    }

    /**
     * Process the update queue
     *
     * @param float|int|null $timeLimit
     * @param int|null $workerID
     * @return string[]
     */
    public function cron($timeLimit = null, $workerID = null)
    {
        return $this->handler->cron($timeLimit, $workerID);
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
     * @throws ClientException
     */
    public function invalidate($baseUri)
    {
        $parser = new UriParser($baseUri);
        return $this->handler->invalidate($parser->base());
    }
}
