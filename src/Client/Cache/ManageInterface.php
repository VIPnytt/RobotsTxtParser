<?php
/**
 * vipnytt/RobotsTxtParser
 *
 * @link https://github.com/VIPnytt/RobotsTxtParser
 * @license https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE The MIT License (MIT)
 */

namespace vipnytt\RobotsTxtParser\Client\Cache;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Interface ManageInterface
 *
 * @see https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/methods/Cache.md for documentation
 * @package vipnytt\RobotsTxtParser\Client\Cache
 */
interface ManageInterface extends CacheInterface, RobotsTxtInterface
{
    /**
     * Default cleaning delay in seconds
     */
    const CLEAN_DELAY = 3600;
    /**
     * Default cron execution time, in seconds
     */
    const CRON_EXEC_TIME = 60;
    /**
     * Default worker ID
     */
    const WORKER_ID = null;
    /**
     * Default cURL options
     */
    const CURL_OPTIONS = [];

    /**
     * ManageInterface constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo);

    /**
     * Set byte limit
     *
     * @param int|null $bytes
     * @return bool
     */
    public function setByteLimit($bytes = RobotsTxtInterface::BYTE_LIMIT);

    /**
     * Set cURL options
     *
     * @param array $options
     * @return bool
     */
    public function setCurlOptions(array $options = self::CURL_OPTIONS);

    /**
     * Process the update queue
     *
     * @param float|int $timeLimit
     * @param int|null $workerID
     * @return string[]
     */
    public function cron($timeLimit = self::CRON_EXEC_TIME, $workerID = self::WORKER_ID);

    /**
     * Clean the cache table
     *
     * @param int $delay
     * @return bool
     */
    public function clean($delay = self::CLEAN_DELAY);

    /**
     * Base class
     *
     * @param string $baseUri
     * @return BaseInterface
     */
    public function base($baseUri);
}
